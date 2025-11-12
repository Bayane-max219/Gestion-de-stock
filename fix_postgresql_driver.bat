@echo off
echo 🔧 ACTIVATION DRIVER POSTGRESQL POUR PHP
echo =======================================

echo 📍 Localisation du fichier php.ini...

set PHP_INI="C:\wamp64\bin\php\php8.2.0\php.ini"

if not exist %PHP_INI% (
    echo ❌ Fichier php.ini non trouvé: %PHP_INI%
    echo 💡 Vérifiez le chemin WAMP
    pause
    exit /b 1
)

echo ✅ Fichier php.ini trouvé: %PHP_INI%
echo.

echo 🔍 Vérification des extensions PostgreSQL...
findstr /C:"extension=pdo_pgsql" %PHP_INI% >nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ pdo_pgsql trouvé dans php.ini
) else (
    echo ❌ pdo_pgsql non trouvé
)

findstr /C:"extension=pgsql" %PHP_INI% >nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ pgsql trouvé dans php.ini
) else (
    echo ❌ pgsql non trouvé
)

echo.
echo 🛠️ SOLUTION MANUELLE:
echo 1. Ouvrez: %PHP_INI%
echo 2. Cherchez les lignes:
echo    ;extension=pdo_pgsql
echo    ;extension=pgsql
echo 3. Supprimez le ; devant ces lignes
echo 4. Sauvegardez le fichier
echo 5. Redémarrez WAMP
echo.

echo 💡 Ou utilisez WAMP Manager:
echo    - Clic droit sur icône WAMP
echo    - PHP ^> PHP Extensions
echo    - Cochez pdo_pgsql et pgsql
echo.

pause
