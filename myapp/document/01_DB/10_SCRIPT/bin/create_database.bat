set curpath=%~dp0

rem �ʂ̃e�[�u����`�� all.sql �Ƀt�@�C�����o�͂���
copy /b "%curpath%\..\ddl\CREATE_TABLE\TABLE_*"+"%curpath%\..\ddl\CREATE_TABLE\CONST_*" "%curpath%\..\ddl\CREATE_TABLE\all.sql" > nul

call "%curpath%\env.bat"

%mysql% %option% -h %host% -u %user% -p%password% < "%curpath%\..\ddl\CREATE_DB\db.sql"

IF NOT %ERRORLEVEL% == 0 (
	echo db.sql:�X�N���v�g�ŃG���[���������܂����B >> "%curpath%\..\result.txt"
)

%mysql% %option% -h %host% %database% -u %user% -p%password% < "%curpath%\..\ddl\CREATE_DB\user.sql"

IF NOT %ERRORLEVEL% == 0 (
	echo user.sql:�X�N���v�g�ŃG���[���������܂����B >> "%curpath%\..\result.txt"
)

%mysql% %option% -h %host% %database% -u %user% -p%password% < "%curpath%\..\ddl\CREATE_TABLE\all.sql"

IF NOT %ERRORLEVEL% == 0 (
	echo all.sql:�X�N���v�g�ŃG���[���������܂����B >> "%curpath%\..\result.txt"
)
