#!/bin/bash

read -p "Enter the number of the emulators you want switched off: " no_of_emulators

for i in {1..$no_of_emulators}
do
   read -p "Enter the ID of the emulator you want switched off: " emulator
   adb -s $emulator emu kill 
done