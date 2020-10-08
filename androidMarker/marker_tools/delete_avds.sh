#!/bin/bash

if [ "$ANDROID_SDK_ROOT" == "" ];
then
  echo "No sdk root ($ANDROID_SDK_ROOT)."
  exit 1
fi
cd $ANDROID_SDK_ROOT/tools
avdmanager.bat delete avd -n $1
echo "Emulator $1 has been deleted."

