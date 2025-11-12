@echo off
echo 🎯 TEST LARAVEL FINAL
echo ====================

echo 📍 Arrêt processus...
taskkill /F /IM php.exe 2>nul

echo 📍 Nettoyage cache...
cd backend
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear 2>nul

echo 📍 Test syntaxe routes...
C:\wamp64\bin\php\php8.2.0\php.exe -l routes\api.php
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Erreur syntaxe routes
    pause
    exit /b 1
)

echo 📍 Test syntaxe contrôleur...
C:\wamp64\bin\php\php8.2.0\php.exe -l app\Http\Controllers\Api\AuthControllerClean.php
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Erreur syntaxe contrôleur
    pause
    exit /b 1
)

echo ✅ Syntaxe OK !

echo 📍 Démarrage Laravel...
start "Laravel Final" cmd /k "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo 📍 Test API health...
curl -s "http://127.0.0.1:8000/api/health"

echo.
echo 📍 Test API login...
curl -s -X POST "http://127.0.0.1:8000/api/login" -H "Content-Type: application/json" -d "{\"email\":\"franco@gmail.com\",\"password\":\"I love teko.\"}"

echo.
echo.
echo 🎯 Si les tests passent, allez sur http://localhost:5174
echo.

pause
