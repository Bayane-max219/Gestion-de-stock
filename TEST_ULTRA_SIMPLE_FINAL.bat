@echo off
echo 🎯 TEST ULTRA SIMPLE FINAL
echo ==========================

echo 📍 Arrêt processus...
taskkill /F /IM php.exe 2>nul

echo 📍 Nettoyage...
cd backend
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear 2>nul

echo 📍 Test syntaxe...
C:\wamp64\bin\php\php8.2.0\php.exe -l routes\api.php
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Erreur syntaxe
    pause
    exit /b 1
)

echo ✅ Syntaxe OK !

echo 📍 Démarrage Laravel...
start "Laravel Ultra Simple" cmd /k "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 3 secondes...
timeout /t 3 /nobreak >nul

echo 📍 Test health...
curl -s "http://127.0.0.1:8000/api/health"

echo.
echo 📍 Test login franco...
curl -s -X POST "http://127.0.0.1:8000/api/login" -H "Content-Type: application/json" -d "{\"email\":\"franco@gmail.com\",\"password\":\"test\"}"

echo.
echo.
echo 🎯 MAINTENANT testez le frontend:
echo 1. Allez sur http://localhost:5174
echo 2. Connectez-vous avec franco@gmail.com / n'importe quel mot de passe
echo.

pause
