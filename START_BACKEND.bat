@echo off
echo 🚀 DÉMARRAGE BACKEND LARAVEL SMARTERP PRO
echo =======================================

echo 📍 Vérification de l'environnement...

REM Vérifier si nous sommes dans le bon répertoire
if not exist "backend-laravel" (
    echo ❌ Répertoire backend-laravel non trouvé
    echo 💡 Exécutez ce script depuis le répertoire racine du projet
    pause
    exit /b 1
)

echo ✅ Répertoire backend-laravel trouvé

REM Aller dans le répertoire backend-laravel
cd backend-laravel

echo 📍 Vérification de la configuration Laravel...

REM Vérifier si .env existe
if not exist ".env" (
    echo ❌ Fichier .env manquant
    echo 💡 Copiez .env.example vers .env et configurez-le
    pause
    exit /b 1
)

echo ✅ Fichier .env trouvé

echo 📍 Test de connexion MySQL...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT 'MySQL OK' as status;" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ MySQL connecté - Base stock_management accessible
) else (
    echo ✅ Structure Laravel trouvée
    cd backend-laravel
    
    if exist "artisan" (
        echo 🎯 Démarrage avec Artisan...
        %PHP_PATH% artisan serve
    ) else (
        echo 🎯 Démarrage serveur PHP simple...
        %PHP_PATH% -S localhost:8000
    )
)

pause
