@echo off
echo 🚀 SETUP LARAVEL AVEC WAMP64 + POSTGRESQL
echo ==========================================

set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe
set PROJECT_DIR=C:\Users\Miguel\Desktop\Applikcation Octobre\Projet gestion de stock

echo 🐘 Test PHP...
%PHP_PATH% --version
if %errorlevel% neq 0 (
    echo ❌ Erreur PHP - Vérifiez que WAMP est démarré
    pause
    exit /b 1
)

echo ✅ PHP OK !

echo 🎼 Test Composer...
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Composer non trouvé - Installation nécessaire
    echo 💡 Téléchargez depuis: https://getcomposer.org/download/
    echo 💡 Ou utilisez: %PHP_PATH% composer.phar
    pause
    exit /b 1
)

echo ✅ Composer OK !

echo 📁 Création du projet Laravel...
cd "%PROJECT_DIR%"
composer create-project laravel/laravel backend-laravel

cd backend-laravel

echo 📦 Installation des packages...
composer require laravel/sanctum
composer require fruitcake/laravel-cors

echo 🔧 Configuration PostgreSQL...
copy .env.example .env

echo 🔑 Génération clé...
%PHP_PATH% artisan key:generate

echo 📊 Configuration Sanctum...
%PHP_PATH% artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo ✅ Backend Laravel créé !
echo 🔗 Prochaine étape: Configurer .env pour PostgreSQL

pause
