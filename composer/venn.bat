@echo off
setlocal ENABLEEXTENSIONS ENABLEDELAYEDEXPANSION

echo ################################################################
echo ####################     VENN FRAMEWORK     ####################
echo ################## (C) Agwa Israel Onome 2018 ##################
echo ################################################################
echo: 

set VENN_FOLDER=%~dp0..\Venn
set VENN_PHP_FILE=%~dp0venn.php
IF not exist "%VENN_FOLDER%" (
  echo ERROR!
  echo:
  echo Cannot find Venn directory.
  echo Failed!
  goto end
)

if "%1"=="start" goto start
if "%1"=="update" goto update
goto runphp

:_direct
set /p INSTALL_OPTION= "Use current directory for installation? (y/n) "
if %INSTALL_OPTION%==y (
  set INSTALL_DIR=%cd%
) else (
  set /P INSTALL_DIR="Insert installation path: "
)
if "%INSTALL_DIR%"=="" (
  echo You must specify a path for installation
  goto _direct
)

:start
set PROJECT_NAME=%2
IF "%PROJECT_NAME%"=="" set /p PROJECT_NAME= "Please specify a name for this project: "
IF not "%3"=="" (
  set INSTALL_DIR=%~dpx3
) else (
  call :_direct
)


if not exist "%INSTALL_DIR%" (
  echo Provided installation directory does not exist!
  goto end
) else (
  chdir %INSTALL_DIR%
  goto _check
)

:update
where /Q php.exe > NUL
if not %ERRORLEVEL%==0 (
  echo:
  echo Error!
  echo You must have a php installation to run this command.
  echo Add php installation path to PATH environment variable and try again.
  echo:
  goto end
)
set PHP_ERROR=1
set PROJECT_DIR=%cd%
if not exist "%PROJECT_DIR%\app.json" (
  echo:
  echo Error! 
  echo Current folder is not a project folder!
  goto end
) else (
  php "%~dp0venn.php" "%PROJECT_DIR%" "%VENN_FOLDER%" "%~dp0update" && set PHP_ERROR=0
if !PHP_ERROR!==0 (
  tree "%PROJECT_DIR%"
  echo:
  echo Update complete!
  echo:
) else (
  echo:
  echo Update failed!
)
goto end

)

:_check
if exist "%PROJECT_NAME%" (
    echo Project already exists!
    goto end
) else (
    echo Preparing to install...
    goto _install
)

:_install
set INSTALL_COMPLETE=false
mkdir %PROJECT_NAME%
set CUR_INSTALL_DIR=%cd%
chdir %~dp0
type "%VENN_FOLDER%\none.venn" >exclude
xcopy /Q /E /EXCLUDE:exclude "%VENN_FOLDER%" "%CUR_INSTALL_DIR%\%PROJECT_NAME%" && set INSTALL_COMPLETE=true
del exclude
tree "%CUR_INSTALL_DIR%/%PROJECT_NAME%"
if %INSTALL_COMPLETE%==true (
  echo Installation complete!
  echo:
  echo !!!!!!!!!!!!!!!!!!!!!!!  Congratulations  !!!!!!!!!!!!!!!!!!!!!!!
  echo:
  echo Start building today!
) else (
  rmdir "%CUR_INSTALL_DIR%/%PROJECT_NAME%"
  echo Installation failed!
)
goto end

:runphp
where /Q php.exe > NUL
if not %ERRORLEVEL%==0 (
  echo:
  echo Error!
  echo You must have a php installation to run this command.
  echo Add php installation path to PATH environment variable.
  echo:
  goto end
)
set PHP_ERROR=1
php "%VENN_PHP_FILE%" %* && set PHP_ERROR=0
goto end

:end
echo:
endlocal
pause
exit /B 100