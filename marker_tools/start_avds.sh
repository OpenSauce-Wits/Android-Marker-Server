#!/bin/bash

cd "C:/Users/Olebogeng Maleho/AppData/Local/Android/Sdk/tools/bin"
read -p "Enter the name of the desired AVD : " AVD_NAME

emulator @$AVD_NAME -no-window -no-audio
exec $SHELL
