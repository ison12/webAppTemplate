@echo off

set curpath=%~dp0

set binpath=%curpath%bin\
set ddlpath=%curpath%ddl\
set resultpath=%curpath%result.txt

echo ���s�F%date% %time% >> %resultpath%

call %binpath%env.bat

call %binpath%create_database.bat

echo �����F%date% %time% >> %resultpath%

pause
