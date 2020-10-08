#!/bin/bash
emulator=""
for device in $(adb devices)
do
 if [ "$device" == "$1" ];
 then
 	echo $device
 	emulator=$device
 fi
done

if [ "$emulator" == "" ];
then
 exit 1
else
 adb -s $emulator emu kill 
 rm ~/.android/avd/$emulator.avd/*.lock
 echo "Emulator $emulator has been stopped."
fi

