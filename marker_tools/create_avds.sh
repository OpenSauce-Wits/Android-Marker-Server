#!/bin/bash

#You can get targetID by running avdmanager list

read -p "Provide a name for your new emulator: " AVD_NAME
read -p "Provide the target id for your emulator: " targetID

avdmanager create avd -n $AVD_NAME -d $targetID -k "system-images;android-29;google_apis;x86" 
