@echo off
echo 🚀 LANCEMENT SMARTERP PRO - LARAVEL + VUE.JS
echo ============================================

echo 📍 Vérification de l'environnement...

REM Vérifier WAMP
echo 📍 Vérification MySQL...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "SELECT 'MySQL OK' as status;" 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ WAMP n'est pas démarré - Démarrez WAMP d'abord
    pause
    exit /b 1
)
echo ✅ MySQL connecté

echo.
echo 📍 Démarrage Laravel backend...
start "Laravel Backend" cmd /k "cd backend && echo 🔥 LARAVEL BACKEND DEMARRAGE... && C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 3 secondes pour Laravel...
timeout /t 3 /nobreak >nul

echo 📍 Démarrage Vue.js frontend...
start "Vue.js Frontend" cmd /k "cd frontend && echo 🔥 VUE.JS FRONTEND DEMARRAGE... && npm run dev"

echo.
echo ✅ PROJET SMARTERP PRO LANCÉ !
echo.
echo 🌐 URLs:
echo   - Backend Laravel:  http://localhost:8000
echo   - Frontend Vue.js:  http://localhost:5173
echo   - API Routes:       http://localhost:8000/api
echo.
echo 📝 Pour tester:
echo   1. Allez sur http://localhost:5173
echo   2. Créez un compte (inscription)
echo   3. Connectez-vous avec ce compte
echo.

pause
