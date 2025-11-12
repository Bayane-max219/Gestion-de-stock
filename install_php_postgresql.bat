@echo off
echo 🚀 INSTALLATION PHP AVEC POSTGRESQL
echo ==================================

echo 📍 Téléchargement PHP 8.2 avec extensions PostgreSQL...
echo.

echo 💡 ÉTAPES MANUELLES:
echo.
echo 1. Aller sur: https://windows.php.net/download/
echo 2. Télécharger: PHP 8.2 Thread Safe (x64)
echo 3. Extraire dans: C:\php82-pgsql\
echo 4. Copier php.ini-development vers php.ini
echo 5. Activer les extensions:
echo    - extension=pdo_pgsql
echo    - extension=pgsql
echo.

echo 🔧 ALTERNATIVE RAPIDE:
echo Utiliser Composer avec PHP global:
echo.

REM Vérifier si Composer est installé globalement
where composer >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo ✅ Composer trouvé globalement
    echo 🚀 Test avec Composer...
    cd /d "C:\Users\Miguel\Desktop\Applikcation Octobre\Projet gestion de stock\backend"
    composer install --no-dev
    php artisan migrate
) else (
    echo ❌ Composer non trouvé globalement
    echo.
    echo 💡 SOLUTION IMMÉDIATE:
    echo 1. Installer PHP avec extensions PostgreSQL
    echo 2. Ou configurer WAMP avec les bonnes extensions
    echo.
    echo 📋 EXTENSIONS WAMP MANQUANTES:
    echo - Télécharger php_pdo_pgsql.dll pour PHP 8.2
    echo - Télécharger php_pgsql.dll pour PHP 8.2
    echo - Les placer dans: C:\wamp64\bin\php\php8.2.0\ext\
    echo - Activer dans php.ini
)

echo.
pause
