set curpath=%~dp0

if "%1" == "" (
	echo ����1�ɁA���s������SQL���i�[����Ă���t�H���_�p�X���w�肵�Ă��������B
	exit /b 1
)

call %curpath%\env.bat

for /r "%~1" %%i in (*.sql) do (

	call %curpath%\import_data_sub.bat %%i
	IF NOT %ERRORLEVEL% == 0 (
		echo %%i:�X�N���v�g�ŃG���[���������܂����B >> "%curpath%\..\result.txt"
	)
	
) 
