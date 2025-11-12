@echo off
echo 🐘 CRÉATION DE LA BASE POSTGRESQL
echo ==================================

echo 📊 Création de la base smarterp_pro...
psql -U postgres -h localhost -c "DROP DATABASE IF EXISTS smarterp_pro;"
psql -U postgres -h localhost -c "CREATE DATABASE smarterp_pro WITH OWNER = postgres ENCODING = 'UTF8';"

echo ✅ Base de données créée avec succès !
echo 🔗 Connexion : postgresql://postgres:postgres@localhost:5432/smarterp_pro

pause
