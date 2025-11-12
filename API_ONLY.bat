@echo off
echo 🎯 API PURE SMARTERP PRO
echo ========================

echo 📍 Démarrage serveur PHP pur sur port 8002...
cd backend\public
C:\wamp64\bin\php\php8.2.0\php.exe -S localhost:8002 -t . index.php

pause
