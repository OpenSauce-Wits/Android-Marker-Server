#!/bin/bash

read -p "Enter the name of the desired AVD : " AVD_NAME

emulator @$AVD_NAME -no-window -no-audio
