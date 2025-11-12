@echo off
echo 🚀 MIGRATION LARAVEL AVEC PHP POSTGRESQL
echo =======================================

echo 📍 Recherche de PHP avec support PostgreSQL...

REM Chemins possibles de PHP avec PostgreSQL
set PHP_PATHS[0]="C:\Program Files\PostgreSQL\16\php\bin\php.exe"
set PHP_PATHS[1]="C:\Program Files\PostgreSQL\15\php\bin\php.exe"
set PHP_PATHS[2]="C:\Program Files\PostgreSQL\14\php\bin\php.exe"
set PHP_PATHS[3]="C:\xampp\php\php.exe"
set PHP_PATHS[4]="C:\php\php.exe"

set PHP_EXE=""

REM Tester chaque chemin
for /L %%i in (0,1,4) do (
    call set "CURRENT_PATH=%%PHP_PATHS[%%i]%%"
    call set "CURRENT_PATH=%%CURRENT_PATH:"=%%"
    if exist "!CURRENT_PATH!" (
        echo 🔍 Test: !CURRENT_PATH!
        "!CURRENT_PATH!" -m | findstr pdo_pgsql >nul
        if !ERRORLEVEL! EQU 0 (
            set "PHP_EXE=!CURRENT_PATH!"
            echo ✅ PHP avec PostgreSQL trouvé: !CURRENT_PATH!
            goto :found
        ) else (
            echo ❌ Pas de support PostgreSQL
        )
    )
)

echo ❌ Aucun PHP avec support PostgreSQL trouvé
echo.
echo 💡 SOLUTIONS:
echo 1. Installer XAMPP (inclut PostgreSQL)
echo 2. Télécharger PHP avec extensions PostgreSQL
echo 3. Configurer WAMP correctement
echo.
pause
exit /b 1

:found
echo.
echo 🔄 Remise en mode PostgreSQL...

REM Remettre PostgreSQL dans .env
cd backend
echo DB_CONNECTION=pgsql > .env.temp
echo DB_HOST=127.0.0.1 >> .env.temp
echo DB_PORT=5432 >> .env.temp
echo DB_DATABASE=stock_management >> .env.temp
echo DB_USERNAME=postgres >> .env.temp
echo DB_PASSWORD=postgres >> .env.temp
echo APP_NAME="Stock Management System" >> .env.temp
echo APP_ENV=local >> .env.temp
echo APP_KEY=base64:3F0EzjqhI/PwBAtRykBFCVEu/kVa2QHF6Ug2e+QepPM= >> .env.temp
echo APP_DEBUG=true >> .env.temp
echo APP_URL=http://localhost:8000 >> .env.temp
echo FRONTEND_URL=http://localhost:5173 >> .env.temp
echo SANCTUM_STATEFUL_DOMAINS=localhost:5173 >> .env.temp
echo SESSION_DOMAIN=localhost >> .env.temp
echo LOG_CHANNEL=stack >> .env.temp
echo LOG_DEPRECATIONS_CHANNEL=null >> .env.temp
echo LOG_LEVEL=debug >> .env.temp

move .env.temp .env

echo ✅ Configuration PostgreSQL restaurée
echo.
echo 🚀 Exécution des migrations...
"%PHP_EXE%" artisan migrate

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ MIGRATIONS RÉUSSIES !
    echo 🗄️ Tables créées dans PostgreSQL
    echo.
    echo 🚀 Démarrage du serveur Laravel...
    "%PHP_EXE%" artisan serve
) else (
    echo.
    echo ❌ Erreur lors des migrations
)

pause
