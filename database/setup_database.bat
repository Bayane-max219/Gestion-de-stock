@echo off
echo ========================================
echo    SmartERP Pro - Configuration PostgreSQL
echo ========================================
echo.

echo 1. Creation de la base de donnees...
psql -U postgres -f create_database.sql

echo.
echo 2. Creation des tables...
psql -U postgres -d smarterp_pro -f create_tables.sql

echo.
echo 3. Insertion des donnees d'exemple...
psql -U postgres -d smarterp_pro -f insert_sample_data.sql

echo.
echo ========================================
echo    Configuration terminee avec succes !
echo ========================================
echo.
echo Donnees de connexion :
echo - Base de donnees : smarterp_pro
echo - Utilisateur : postgres
echo - Port : 5432
echo.
echo Comptes de test crees :
echo - admin@demo.com / password123 (Epicerie Rakoto)
echo - rabe@boutique.mg / password123 (Superette Rabe)
echo - andry@quinca.mg / password123 (Quincaillerie Andry)
echo.
pause
