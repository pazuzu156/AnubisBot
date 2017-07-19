@echo off

if "%1" NEQ "" (
    if "%1" EQU "-c" goto clean
)

:download
echo Downloading ApiGen
if exist apigen.phar goto build
powershell -Command "Invoke-WebRequest https://github.com/ApiGen/ApiGen.github.io/raw/master/apigen.phar -OutFile apigen.phar"

:build
echo Building API
php apigen.phar generate --source core --destination reference --template-theme bootstrap --deprecated --download --tree --title="AnubisBot Reference"
goto end

:clean
echo Cleaning ApiGen stuff
if exist reference rmdir /S /Q reference
if exist apigen.phar del apigen.phar

:end
