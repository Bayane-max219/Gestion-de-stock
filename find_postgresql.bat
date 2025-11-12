@echo off
echo 🔍 RECHERCHE DE POSTGRESQL
echo ==========================

echo 📁 Vérification des chemins possibles...

if exist "C:\Program Files\PostgreSQL\16\bin\psql.exe" (
    echo ✅ Trouvé: C:\Program Files\PostgreSQL\16\bin\psql.exe
    set PSQL_PATH="C:\Program Files\PostgreSQL\16\bin\psql.exe"
    goto :found
)

if exist "C:\Programmes\PostgreSQL\16\bin\psql.exe" (
    echo ✅ Trouvé: C:\Programmes\PostgreSQL\16\bin\psql.exe
    set PSQL_PATH="C:\Programmes\PostgreSQL\16\bin\psql.exe"
    goto :found
)

if exist "C:\Program Files\PostgreSQL\15\bin\psql.exe" (
    echo ✅ Trouvé: C:\Program Files\PostgreSQL\15\bin\psql.exe
    set PSQL_PATH="C:\Program Files\PostgreSQL\15\bin\psql.exe"
    goto :found
)

if exist "C:\Program Files\PostgreSQL\14\bin\psql.exe" (
    echo ✅ Trouvé: C:\Program Files\PostgreSQL\14\bin\psql.exe
    set PSQL_PATH="C:\Program Files\PostgreSQL\14\bin\psql.exe"
    goto :found
)

echo ❌ PostgreSQL non trouvé dans les chemins standards
echo 🔍 Recherche sur tout le disque C:...
dir /s C:\psql.exe 2>nul
echo.
echo 💡 Si PostgreSQL est installé, vérifiez dans:
echo    - Menu Démarrer → PostgreSQL
echo    - Panneau de configuration → Programmes
goto :end

:found
echo 🧪 Test de connexion...
%PSQL_PATH% -U postgres -h localhost -c "SELECT version();" 2>nul
if %errorlevel% equ 0 (
    echo ✅ Connexion PostgreSQL OK !
    echo 🚀 Création de la base smarterp_pro...
    %PSQL_PATH% -U postgres -h localhost -c "DROP DATABASE IF EXISTS smarterp_pro;"
    %PSQL_PATH% -U postgres -h localhost -c "CREATE DATABASE smarterp_pro;"
    echo ✅ Base smarterp_pro créée !
) else (
    echo ❌ Erreur de connexion - Vérifiez le mot de passe
    echo 💡 Mot de passe par défaut: postgres
)

:end
pause
