@echo off
echo 🔧 RECONSTRUCTION LARAVEL PROPRE
echo =================================

echo 📍 Étape 1: Nettoyage...
call CLEAN_LARAVEL_STRUCTURE.bat

echo 📍 Étape 2: Installation routes propres...
cd backend\routes
copy api_clean.php api.php

echo 📍 Étape 3: Configuration CORS dans .env...
cd ..
echo. >> .env
echo CORS_ALLOWED_ORIGINS="http://localhost:5173,http://localhost:5174" >> .env

echo 📍 Étape 4: Test de la structure...
C:\wamp64\bin\php\php8.2.0\php.exe -l routes\api.php
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Erreur de syntaxe dans api.php
    pause
    exit /b 1
)

echo 📍 Étape 5: Test du contrôleur...
C:\wamp64\bin\php\php8.2.0\php.exe -l app\Http\Controllers\Api\AuthControllerClean.php
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Erreur de syntaxe dans AuthControllerClean
    pause
    exit /b 1
)

echo 📍 Étape 6: Démarrage Laravel propre...
echo ✅ Structure Laravel reconstruite !
echo 🚀 Démarrage du serveur...

C:\wamp64\bin\php\php8.2.0\php.exe artisan serve

pause
