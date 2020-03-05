set curpath=%~dp0

rem 個別のテーブル定義を all.sql にファイルを出力する
copy /b "%curpath%\..\ddl\CREATE_TABLE\TABLE_*"+"%curpath%\..\ddl\CREATE_TABLE\CONST_*" "%curpath%\..\ddl\CREATE_TABLE\all.sql" > nul

call "%curpath%\env.bat"

%mysql% %option% -h %host% -u %user% -p%password% < "%curpath%\..\ddl\CREATE_DB\db.sql"

IF NOT %ERRORLEVEL% == 0 (
	echo db.sql:スクリプトでエラーが発生しました。 >> "%curpath%\..\result.txt"
)

%mysql% %option% -h %host% %database% -u %user% -p%password% < "%curpath%\..\ddl\CREATE_DB\user.sql"

IF NOT %ERRORLEVEL% == 0 (
	echo user.sql:スクリプトでエラーが発生しました。 >> "%curpath%\..\result.txt"
)

%mysql% %option% -h %host% %database% -u %user% -p%password% < "%curpath%\..\ddl\CREATE_TABLE\all.sql"

IF NOT %ERRORLEVEL% == 0 (
	echo all.sql:スクリプトでエラーが発生しました。 >> "%curpath%\..\result.txt"
)
