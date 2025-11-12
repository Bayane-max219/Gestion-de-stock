@echo off
echo 🔄 REDÉMARRAGE API AVEC CORS CORRIGÉ
echo ===================================

echo 📍 Arrêt des processus PHP sur port 8002...
taskkill /F /IM php.exe 2>nul

echo 📍 Attente 2 secondes...
timeout /t 2 /nobreak >nul

echo 📍 Redémarrage API sur port 8002...
C:\wamp64\bin\php\php8.2.0\php.exe -S localhost:8002 api_simple.php

pause
