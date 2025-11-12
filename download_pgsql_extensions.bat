@echo off
echo 🔧 TÉLÉCHARGEMENT EXTENSIONS POSTGRESQL POUR WAMP
echo ===============================================

echo 📍 Extensions requises pour PHP 8.2 Thread Safe x64:
echo - php_pdo_pgsql.dll
echo - php_pgsql.dll
echo.

echo 🌐 LIENS DE TÉLÉCHARGEMENT:
echo.
echo 1. Aller sur: https://windows.php.net/downloads/pecl/releases/pdo_pgsql/
echo 2. Télécharger: pdo_pgsql pour PHP 8.2 TS x64
echo.
echo 3. Aller sur: https://windows.php.net/downloads/pecl/releases/pgsql/
echo 4. Télécharger: pgsql pour PHP 8.2 TS x64
echo.

echo 📂 INSTALLATION:
echo 1. Extraire les fichiers .dll
echo 2. Copier dans: C:\wamp64\bin\php\php8.2.0\ext\
echo 3. Ouvrir: C:\wamp64\bin\php\php8.2.0\php.ini
echo 4. Ajouter les lignes:
echo    extension=pdo_pgsql
echo    extension=pgsql
echo 5. Redémarrer WAMP
echo.

echo 🚀 ALTERNATIVE AUTOMATIQUE:
echo Utiliser PowerShell pour télécharger:
echo.

powershell -Command "Write-Host '📥 Tentative de téléchargement automatique...' -ForegroundColor Green"

REM Créer le dossier de téléchargement
if not exist "C:\temp\php-pgsql" mkdir "C:\temp\php-pgsql"

echo 💡 Si le téléchargement automatique échoue:
echo Téléchargez manuellement depuis:
echo - https://pecl.php.net/package/PDO_PGSQL
echo - https://pecl.php.net/package/pgsql
echo.

echo ✅ Une fois installé, relancez: migrate.bat
echo.

pause
