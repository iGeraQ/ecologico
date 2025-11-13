@echo off
if "%~1"=="" (
	echo Error: Debe proporcionar el nombre del archivo como parametro
	echo Uso: %0 nombre_archivo.php
	exit /b 1
)

set TEST_FILE=%~1

C:\xampp\php\php.exe .\phpunit.phar --bootstrap test/%TEST_FILE% tests