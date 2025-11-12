@echo off
echo 🚀 CRÉATION LARAVEL RÉEL - MAINTENANT !
echo ========================================

set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe

echo 🗑️ Suppression ancien dossier...
if exist "backend-laravel" rmdir /s /q "backend-laravel"

echo 📦 Création Laravel...
%PHP_PATH% composer.phar create-project --prefer-dist laravel/laravel backend-laravel

echo ⏳ Attente création...
timeout /t 5 /nobreak >nul

if exist "backend-laravel\artisan" (
    echo ✅ Laravel créé avec succès !
    
    cd backend-laravel
    
    echo 🔧 Configuration PostgreSQL...
    echo APP_NAME="SmartERP Pro API" > .env
    echo APP_ENV=local >> .env
    echo APP_DEBUG=true >> .env
    echo APP_URL=http://localhost:8000 >> .env
    echo. >> .env
    echo LOG_CHANNEL=stack >> .env
    echo LOG_LEVEL=debug >> .env
    echo. >> .env
    echo DB_CONNECTION=pgsql >> .env
    echo DB_HOST=127.0.0.1 >> .env
    echo DB_PORT=5432 >> .env
    echo DB_DATABASE=smarterp_pro >> .env
    echo DB_USERNAME=postgres >> .env
    echo DB_PASSWORD=postgres >> .env
    
    echo 🔑 Génération clé...
    %PHP_PATH% artisan key:generate
    
    echo 📊 Installation Sanctum...
    %PHP_PATH% ..\composer.phar require laravel/sanctum
    %PHP_PATH% artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    
    echo 🧪 Test Laravel...
    %PHP_PATH% artisan --version
    
    echo ✅ LARAVEL RÉEL INSTALLÉ !
    echo 🌐 Démarrer avec: php artisan serve
    
) else (
    echo ❌ Erreur création Laravel
    echo 💡 Vérifiez la connexion internet
)

pause
