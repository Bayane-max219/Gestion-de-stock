@echo off
echo 🧹 NETTOYAGE PROJET SMARTERP PRO
echo ==============================

echo 📍 Suppression des fichiers inutiles...

REM Supprimer tous les fichiers de test et scripts inutiles
del /Q "create_smarterp_database.sql" 2>nul
del /Q "create_mysql_database.sql" 2>nul
del /Q "download_pgsql_extensions.bat" 2>nul
del /Q "fix_postgresql_driver.bat" 2>nul
del /Q "fix_wamp_postgresql.bat" 2>nul
del /Q "install_php_postgresql.bat" 2>nul
del /Q "migrate_with_postgresql_php.bat" 2>nul
del /Q "setup_mysql.bat" 2>nul
del /Q "setup_postgresql_smarterp.bat" 2>nul
del /Q "test_api_laravel.bat" 2>nul
del /Q "test_integration_complete.bat" 2>nul
del /Q "tables_mysql_clean.txt" 2>nul
del /Q "CREATE_REAL_LARAVEL.bat" 2>nul
del /Q "INSTALL_LARAVEL_MANUAL.bat" 2>nul

REM Supprimer le faux backend
rmdir /S /Q "backend-laravel" 2>nul

echo ✅ Fichiers inutiles supprimés
echo.
echo 📁 STRUCTURE FINALE:
echo - backend/     (Laravel API)
echo - frontend/    (Vue.js)
echo - database/    (Corrections si nécessaire)
echo.

pause
