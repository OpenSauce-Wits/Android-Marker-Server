#!/bin/bash

cd "C:/Users/Olebogeng Maleho/AppData/Local/Android/Sdk/tools/bin"
read -p "Enter the name of the AVD you want to delete: " AVD_NAME

avdmanager.bat delete avd -n $AVD_NAME
exec $SHELL

