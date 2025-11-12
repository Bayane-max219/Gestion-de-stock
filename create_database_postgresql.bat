@echo off
echo 🐘 CRÉATION DE LA BASE POSTGRESQL
echo ==================================

set PSQL_PATH="C:\Programmes\PostgreSQL\16\bin\psql.exe"
set PGPASSWORD=postgres

echo 📊 Test de connexion PostgreSQL...
%PSQL_PATH% -U postgres -h localhost -c "SELECT version();"

if %errorlevel% neq 0 (
    echo ❌ Erreur de connexion PostgreSQL
    echo 💡 Vérifiez que le service PostgreSQL est démarré
    echo 💡 Vérifiez le mot de passe (par défaut: postgres)
    pause
    exit /b 1
)

echo 🗄️ Suppression de l'ancienne base (si existe)...
%PSQL_PATH% -U postgres -h localhost -c "DROP DATABASE IF EXISTS smarterp_pro;"

echo 📊 Création de la nouvelle base smarterp_pro...
%PSQL_PATH% -U postgres -h localhost -c "CREATE DATABASE smarterp_pro WITH OWNER = postgres ENCODING = 'UTF8';"

echo ✅ Base de données créée avec succès !
echo 🔗 Connexion : postgresql://postgres:postgres@localhost:5432/smarterp_pro

echo 🧪 Test de la base créée...
%PSQL_PATH% -U postgres -h localhost -d smarterp_pro -c "SELECT 'Base smarterp_pro OK' as status;"

pause
