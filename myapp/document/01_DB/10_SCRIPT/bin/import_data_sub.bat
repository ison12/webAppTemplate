set curpath=%~dp0

call %curpath%\env.bat
call %curpath%\basename.bat "%1"

echo %FILE_NAME_WITHOUT_EXT%:�f�[�^����

%mysql% %option% -h %host% %database% -u %user% -p%password% -e"truncate %FILE_NAME_WITHOUT_EXT%;"
IF NOT %ERRORLEVEL% == 0 (
	exit /b %ERRORLEVEL%
)

%mysql% %option% -h %host% %database% -u %user% -p%password% < "%1"
IF NOT %ERRORLEVEL% == 0 (
	exit /b %ERRORLEVEL%
)

exit /b 0