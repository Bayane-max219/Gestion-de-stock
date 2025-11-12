@echo off
echo 🔧 CORRECTION POSTGRESQL POUR WAMP
echo =================================

echo 📍 Vérification des extensions WAMP...

set PHP_EXT_DIR="C:\wamp64\bin\php\php8.2.0\ext"
set PHP_INI="C:\wamp64\bin\php\php8.2.0\php.ini"

echo 🔍 Dossier extensions: %PHP_EXT_DIR%
echo 🔍 Fichier php.ini: %PHP_INI%

echo.
echo 📋 Extensions PostgreSQL requises:
echo - php_pdo_pgsql.dll
echo - php_pgsql.dll

if exist "%PHP_EXT_DIR%\php_pdo_pgsql.dll" (
    echo ✅ php_pdo_pgsql.dll trouvé
) else (
    echo ❌ php_pdo_pgsql.dll MANQUANT
)

if exist "%PHP_EXT_DIR%\php_pgsql.dll" (
    echo ✅ php_pgsql.dll trouvé
) else (
    echo ❌ php_pgsql.dll MANQUANT
)

echo.
echo 🔍 Vérification php.ini...
findstr /C:"extension=pdo_pgsql" %PHP_INI% >nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ pdo_pgsql activé dans php.ini
) else (
    echo ❌ pdo_pgsql NON activé dans php.ini
)

findstr /C:"extension=pgsql" %PHP_INI% >nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ pgsql activé dans php.ini
) else (
    echo ❌ pgsql NON activé dans php.ini
)

echo.
echo 💡 SOLUTIONS:
echo.
echo 1. TÉLÉCHARGER LES EXTENSIONS MANQUANTES:
echo    - Aller sur: https://windows.php.net/downloads/pecl/releases/
echo    - Télécharger pdo_pgsql et pgsql pour PHP 8.2 TS x64
echo    - Copier les .dll dans: %PHP_EXT_DIR%
echo.
echo 2. ACTIVER DANS PHP.INI:
echo    - Ouvrir: %PHP_INI%
echo    - Ajouter les lignes:
echo      extension=pdo_pgsql
echo      extension=pgsql
echo.
echo 3. REDÉMARRER WAMP
echo.

echo 🚀 ALTERNATIVE RAPIDE:
echo Utiliser le PHP de PostgreSQL qui a déjà les extensions:
set PG_PHP="C:\Program Files\PostgreSQL\16\bin\php.exe"
if exist %PG_PHP% (
    echo ✅ PHP PostgreSQL trouvé: %PG_PHP%
    echo.
    echo 🔄 Test avec PHP PostgreSQL...
    %PG_PHP% -m | findstr pdo_pgsql
    if !ERRORLEVEL! EQU 0 (
        echo ✅ Extensions PostgreSQL disponibles !
        echo.
        echo 💡 COMMANDE POUR MIGRATIONS:
        echo %PG_PHP% artisan migrate
    )
) else (
    echo ❌ PHP PostgreSQL non trouvé
)

echo.
pause
