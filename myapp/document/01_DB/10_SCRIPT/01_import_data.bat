@echo off

set curpath=%~dp0

set binpath=%curpath%bin\
set datapath=%curpath%data\
set resultpath=%curpath%result.txt

echo ���s�F%date% %time% >> %resultpath%

call %binpath%env.bat

call %binpath%import_data.bat "%datapath%"

echo �����F%date% %time% >> %resultpath%

pause
