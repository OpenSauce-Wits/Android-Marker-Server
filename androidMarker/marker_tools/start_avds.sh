#!/bin/bash
if [ "$ANDROID_SDK_ROOT" == "" ];
then
  echo "No sdk root ($ANDROID_SDK_ROOT)."
  exit 1
fi

emulator=""
for device in $(adb devices)
do
 if [ "$device" == "$1" ];
 then
 	emulator=$device
 fi
done

if [ "$emulator" == "" ];
then
 cd $ANDROID_SDK_ROOT/tools
 echo "Emulator $1 is running"
 emulator @$1 -no-window -no-audio -no-snapshot
else
 echo "Emulator $emulator is already running."
 exit 1
fi
