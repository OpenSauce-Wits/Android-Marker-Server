#!/bin/bash
# This script create a template, from the LecturerZip.zip and StudentZip,
# to be used for marking
# It assumes there is already an emulator running in the adb

# Declares Variables
rootDir=$(pwd)
androidProject=""
DirFiles=("runTestOnEmulator.sh" "MarkProject.sh" "log.txt")

################################################################################
if [ "$ANDROID_SDK_ROOT" == "" ];
then
  echo "Server:0:error:No sdk root." >> $rootDir/log.txt
  exit 1
fi

# This code searches for available emulators that are running and stores them in
# a list
numDevices=0
AvailableDevices=()

for var in "$@"
do
    numDevices=$(($numDevices + 1))
    AvailableDevices[${#AvailableDevices[@]}]="$var"
done

#php -r "\core\notification::warning(Number of devices: $numDevices);"
if [ "0" == "$numDevices" ];
then
  echo "Server:0:error:No devices to run tests." >> $rootDir/log.txt
  exit 1
fi

################################################################################
# FUNCTIONS
################################################################################

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

open_project
cd "$(dirname "$(find -name *\\gradlew)")"

for emulator in ${AvailableDevices[@]}
do
 ANDROID_SERIAL="$emulator" gradle installDebug 2> $rootDir/log.txt &
done

wait

# numDevices is the number of shards we have
# looping through the list will give us the shard id for each emulator
# The command to run the shards needs specific values that can only be obtained
# in sequence.
# So i will obtain a string of the commands first
count=0
ParallelCommands=()
for device in ${AvailableDevices[@]}
do
 # Now without the & at the end of the command, the marking would not be done in parallel. The shards only split the tests
 ParallelCommands[${#ParallelCommands[@]}]="$device $numDevices $count"
 count=$(($count + 1))
done

for comm in "${ParallelCommands[@]}"
do
 cd "$rootDir"
 ANDROID_SDK_ROOT=$ANDROID_SDK_ROOT PATH=$PATH bash runTestOnEmulator.sh $comm 3>&2 &
done

# The wait makes sure that the computer doesn't carry on with this script before all the tasks are complete
wait

rm -rf "$androidProject"
rm -rf "runTestOnEmulator.sh"
rm -rf "MarkProject.sh"
