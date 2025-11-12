@echo off
echo 🗄️ CRÉATION BASE POSTGRESQL SMARTERP PRO
echo ========================================

echo 📍 Utilisation de PostgreSQL 16...

REM Chemin PostgreSQL 16
set PSQL_EXE="C:\Program Files\PostgreSQL\16\bin\psql.exe"

if not exist %PSQL_EXE% (
    echo ❌ PostgreSQL 16 non trouvé dans C:\Program Files\PostgreSQL\16\bin\
    echo 💡 Vérifiez l'installation PostgreSQL
    pause
    exit /b 1
)

echo ✅ PostgreSQL trouvé: %PSQL_EXE%
echo.
echo 🔧 Création de la base stock_management...
echo 💡 Mot de passe postgres requis
echo.

REM Créer la base directement
%PSQL_EXE% -U postgres -h localhost -p 5432 -c "DROP DATABASE IF EXISTS stock_management;"
%PSQL_EXE% -U postgres -h localhost -p 5432 -c "CREATE DATABASE stock_management OWNER postgres;"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ Base stock_management créée avec succès !
    echo.
    echo 🚀 Prochaine étape: Migrations Laravel
    echo    cd backend
    echo    C:\wamp64\bin\php\php8.2.0\php.exe artisan migrate
) else (
    echo.
    echo ❌ Erreur lors de la création
    echo 💡 Vérifiez que PostgreSQL est démarré
    echo 💡 Vérifiez le mot de passe postgres
)

echo.
pause
