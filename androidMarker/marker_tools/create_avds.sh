#!/bin/bash

if [ "$ANDROID_SDK_ROOT" == "" ];
then
  echo "No sdk root ($ANDROID_SDK_ROOT)."
  exit 1
fi

cd $ANDROID_SDK_ROOT/tools/bin

# $1 AVD_NAME
# $2 target id 

avdmanager create avd -n $1 -d $2 -k "system-images;android-29;google_apis;x86" 
