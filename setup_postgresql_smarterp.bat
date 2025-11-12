@echo off
echo 🗄️ CONFIGURATION POSTGRESQL POUR SMARTERP PRO
echo =============================================

echo 📍 Recherche de PostgreSQL...

REM Chemins possibles de PostgreSQL
set PSQL_PATHS[0]="C:\Program Files\PostgreSQL\15\bin\psql.exe"
set PSQL_PATHS[1]="C:\Program Files\PostgreSQL\14\bin\psql.exe"
set PSQL_PATHS[2]="C:\Program Files\PostgreSQL\13\bin\psql.exe"
set PSQL_PATHS[3]="C:\Program Files\PostgreSQL\16\bin\psql.exe"
set PSQL_PATHS[4]="C:\PostgreSQL\15\bin\psql.exe"
set PSQL_PATHS[5]="C:\PostgreSQL\14\bin\psql.exe"

set PSQL_EXE=""

REM Tester chaque chemin
for /L %%i in (0,1,5) do (
    call set "CURRENT_PATH=%%PSQL_PATHS[%%i]%%"
    call set "CURRENT_PATH=%%CURRENT_PATH:"=%%"
    if exist "!CURRENT_PATH!" (
        set "PSQL_EXE=!CURRENT_PATH!"
        echo ✅ PostgreSQL trouvé: !CURRENT_PATH!
        goto :found
    )
)

echo ❌ PostgreSQL non trouvé dans les emplacements standards
echo 💡 Vérifiez que PostgreSQL est installé
pause
exit /b 1

:found
echo.
echo 🔧 Création de la base de données stock_management...
echo.

REM Exécuter le script SQL
"%PSQL_EXE%" -U postgres -h localhost -p 5432 -f create_smarterp_database.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ Base de données créée avec succès !
    echo 📊 Base: stock_management
    echo 👤 Utilisateur: postgres
    echo 🏠 Serveur: localhost:5432
    echo.
    echo 🚀 Prochaine étape: Exécuter les migrations Laravel
    echo    cd backend
    echo    php artisan migrate
) else (
    echo.
    echo ❌ Erreur lors de la création de la base
    echo 💡 Vérifiez que PostgreSQL est démarré
    echo 💡 Vérifiez le mot de passe postgres
)

echo.
pause
