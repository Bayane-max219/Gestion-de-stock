@echo off
echo 🔧 TEST VERSION MINIMALE
echo ========================

echo 📍 Redémarrage serveur...
taskkill /F /IM php.exe 2>nul
cd backend
start "Laravel Minimal" cmd /k "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 3 secondes...
timeout /t 3 /nobreak >nul

echo 📍 Test health...
curl -s "http://127.0.0.1:8000/api/health"

echo.
echo 📍 Test login Franco...
curl -s -X POST "http://127.0.0.1:8000/api/login" -H "Content-Type: application/json" -d "{\"email\":\"franco@gmail.com\",\"password\":\"I love teko.\"}"

echo.
echo.
echo 🎯 Si ça marche, testez le frontend:
echo 1. Allez sur http://localhost:5174
echo 2. Connectez-vous avec franco@gmail.com / I love teko.
echo.

pause
