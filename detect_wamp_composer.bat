@echo off
echo 🔍 DÉTECTION WAMP + COMPOSER
echo ============================

echo 📁 Recherche de WAMP...
if exist "C:\wamp64\bin\php" (
    echo ✅ WAMP64 trouvé dans C:\wamp64\
    set WAMP_PATH=C:\wamp64
    goto :check_php
)

if exist "C:\wamp\bin\php" (
    echo ✅ WAMP trouvé dans C:\wamp\
    set WAMP_PATH=C:\wamp
    goto :check_php
)

if exist "C:\Program Files\wamp64\bin\php" (
    echo ✅ WAMP64 trouvé dans C:\Program Files\wamp64\
    set WAMP_PATH=C:\Program Files\wamp64
    goto :check_php
)

echo ❌ WAMP non trouvé dans les emplacements standards
echo 💡 Vérifiez si WAMP est installé et démarré
goto :check_composer

:check_php
echo 🐘 Test PHP...
for /d %%i in ("%WAMP_PATH%\bin\php\php*") do (
    set PHP_PATH=%%i
    goto :found_php
)

:found_php
echo ✅ PHP trouvé: %PHP_PATH%
"%PHP_PATH%\php.exe" --version
echo.

:check_composer
echo 🎼 Recherche de Composer...
where composer >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ Composer trouvé dans PATH
    composer --version
) else (
    echo ❌ Composer non trouvé dans PATH
    echo 🔍 Recherche dans les emplacements standards...
    
    if exist "C:\ProgramData\ComposerSetup\bin\composer.bat" (
        echo ✅ Composer trouvé: C:\ProgramData\ComposerSetup\bin\composer.bat
        "C:\ProgramData\ComposerSetup\bin\composer.bat" --version
    ) else if exist "C:\Users\%USERNAME%\AppData\Roaming\Composer\vendor\bin\composer.bat" (
        echo ✅ Composer trouvé: C:\Users\%USERNAME%\AppData\Roaming\Composer\vendor\bin\composer.bat
        "C:\Users\%USERNAME%\AppData\Roaming\Composer\vendor\bin\composer.bat" --version
    ) else (
        echo ❌ Composer non installé
        echo 💡 Téléchargez depuis: https://getcomposer.org/download/
    )
)

echo.
echo 🎯 RÉSUMÉ:
if defined WAMP_PATH echo ✅ WAMP: %WAMP_PATH%
if defined PHP_PATH echo ✅ PHP: %PHP_PATH%
echo.

pause
