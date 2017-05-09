@echo off

rem Replace version with your jdk version
set JAVA_HOME=%PROGRAMFILES%\Java\jdk1.8.0_121

if "%1" NEQ "" (
	if "%1" EQU "-b" (
		goto build
	) else if "%1" EQU "-c" (
		goto clean
	) else (
		goto error
	)
)

:build
mvn install
if errorlevel 1 goto error
goto end

:clean
mvn clean
if errorlevel 1 goto error
goto end

:error
echo error building project
goto end

:end
