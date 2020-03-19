set curpath=%~dp0

if "%1" == "" (
	echo 引数1に、実行したいSQLが格納されているフォルダパスを指定してください。
	exit /b 1
)

call %curpath%\env.bat

for /r "%~1" %%i in (*.sql) do (

	call %curpath%\import_data_sub.bat %%i
	IF NOT %ERRORLEVEL% == 0 (
		echo %%i:スクリプトでエラーが発生しました。 >> "%curpath%\..\result.txt"
	)
	
) 
