#!/bin/bash
# This script create a template, from the LecturerZip.zip and StudentZip,
# to be used for marking

# Declares Variables
textFile="RequiredDocuments.txt"
RequiredDocuments=()
rootDir=$(pwd)
androidProject=""
DirFiles=("RequiredDocuments.txt" "log.txt" "runTestOnEmulator.sh" "LecturerZip.zip" "StudentZip.zip" "MarkProject.sh" "BuildProject.sh")
touch log.txt

################################################################################
if [ "$ANDROID_SDK_ROOT" == "" ];
then
  echo "Server:0:error:No sdk root." >> $rootDir/log.txt
  exit 1
fi

################################################################################
# FUNCTIONS
################################################################################

# Deletes all files that aren't "RequiredDocuments", "PrepareTemplate.sh",
# "LecturerZip.zip" and "StudentZip.zip"
clear_files () {
  for file in $(ls)
  do
    if [[ ! " ${DirFiles[@]} " =~ " ${file} " ]];
    then
      rm -rf "$file"
    fi
  done
}

#Opens the project
open_project () {
  for file in $(ls)
  do
    if [[ ! " ${DirFiles[@]} " =~ " ${file} " ]];
    then
    	androidProject="$file"
      	cd "$file"
    fi
  done
}

################################################################################
# Starts by clearing all files that are not Required
clear_files

# Check that we have all the files that we require
FilesAreMissing=false
for file in ${DirFiles[@]}
do
 if [ ! -f "$file" ]; then
  echo "Server:0:error:Could not find file: $file." >> $rootDir/log.txt
  FilesAreMissing=true
 fi
done
if $FilesAreMissing; then
  exit 1
fi

# Reads in the RequiredDocuments lines from the RequiredDocuments text file and
# stores them in the RequiredDocuments array.
while IFS= read -r line
do
  RequiredDocuments[${#RequiredDocuments[@]}]="$line"
done < "$textFile"

# Unzip the LecturerZip.zip
unzip LecturerZip.zip >/dev/null

# Checks if all the RequiredDocuments exist and deletes them
# Also stores the parent directories of the documnets
dir=""
ParentDirectories=()
for doc in ${RequiredDocuments[@]}
do
  dir=$(find -name *\\$doc)
  if [ "$dir" == "" ];
  then
    # Should terminate script
    echo "Server:0:error:$doc not found in submission." >> $rootDir/log.txt
    FilesAreMissing=true
  else
    ParentDirectories[${#ParentDirectories[@]}]="$(dirname "$dir")"
    rm -f "$dir"
  fi
done
if $FilesAreMissing; then
  exit 1
fi

# By now the documnets in RequiredDocuments.txt have been deleted.
# Searching for them will only bring up the files in the StudentZip

# Make directory to store student Code
mkdir StudentCode

# Unzip the StudentZip.zip
unzip StudentZip.zip -d StudentCode >/dev/null

# Checks if all the RequiredDocuments exist in the student's code and copies
# them into the template.
count=0
for doc in ${RequiredDocuments[@]}
do
  dir=$(find -name *\\$doc)
  if [ "$dir" == "" ];
  then
    # Should terminate script
    echo "$doc:0:error:File not found." >> $rootDir/log.txt
    FilesAreMissing=true
  else
    cp -R "$dir" ${ParentDirectories[$count]}
    count=$(($count + 1))
  fi
done
if $FilesAreMissing; then
  exit 1
fi

# Removes the Student's code folder
rm -rf StudentCode

open_project
cd "$(dirname "$(find -name *\\gradlew)")"
#echo "$(pwd)"> $rootDir/log.txt
gradle assembleDebugAndroidTest 2> $rootDir/log.txt

cd $rootDir
rm -rf "RequiredDocuments.txt"
rm -rf "LecturerZip.zip"
rm -rf "StudentZip.zip"
rm -rf "BuildProject.sh"
