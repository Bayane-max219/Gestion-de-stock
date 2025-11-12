@echo off
echo 🎯 TEST ULTRA SIMPLE LARAVEL
echo ============================

echo 📍 Arrêt processus...
taskkill /F /IM php.exe 2>nul

echo 📍 Nettoyage...
cd backend
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear 2>nul

echo 📍 Démarrage Laravel...
start "Laravel Test" cmd /k "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo 📍 Test health...
curl -s "http://127.0.0.1:8000/api/health"

echo.
echo 📍 Test login simple...
curl -s -X POST "http://127.0.0.1:8000/api/login" -H "Content-Type: application/json" -d "{\"email\":\"franco@gmail.com\",\"password\":\"test\"}"

echo.
echo.
echo 🎯 Si ça marche, testez le frontend sur http://localhost:5174
echo.

pause
