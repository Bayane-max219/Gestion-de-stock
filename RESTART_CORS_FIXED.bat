@echo off
echo 🔧 REDÉMARRAGE CORS CORRIGÉ
echo ===========================

echo 📍 Arrêt serveur...
taskkill /F /IM php.exe 2>nul

echo 📍 Redémarrage...
cd backend
start "Laravel CORS Fixed" cmd /k "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 3 secondes...
timeout /t 3 /nobreak >nul

echo 📍 Test health...
curl -s "http://127.0.0.1:8000/api/health"

echo.
echo.
echo 🎯 CORS corrigé ! Testez maintenant:
echo 1. Allez sur http://localhost:5174
echo 2. Testez l'inscription avec un nouveau compte
echo 3. Testez la connexion avec franco@gmail.com
echo.

pause
