@echo off
echo 🚀 INSTALLATION LARAVEL MANUELLE
echo =================================

set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe

echo 📥 Téléchargement Laravel ZIP...
powershell -Command "& {Invoke-WebRequest -Uri 'https://github.com/laravel/laravel/archive/refs/heads/10.x.zip' -OutFile 'laravel.zip'}"

if exist "laravel.zip" (
    echo ✅ Laravel ZIP téléchargé
    
    echo 📂 Extraction...
    powershell -Command "& {Expand-Archive -Path 'laravel.zip' -DestinationPath '.' -Force}"
    
    if exist "laravel-10.x" (
        echo 📁 Renommage en backend-laravel...
        if exist "backend-laravel" rmdir /s /q "backend-laravel"
        ren "laravel-10.x" "backend-laravel"
        
        cd backend-laravel
        
        echo 🔧 Configuration .env...
        copy .env.example .env
        
        echo APP_NAME="SmartERP Pro API" > .env
        echo APP_ENV=local >> .env
        echo APP_DEBUG=true >> .env
        echo APP_URL=http://localhost:8000 >> .env
        echo. >> .env
        echo DB_CONNECTION=pgsql >> .env
        echo DB_HOST=127.0.0.1 >> .env
        echo DB_PORT=5432 >> .env
        echo DB_DATABASE=smarterp_pro >> .env
        echo DB_USERNAME=postgres >> .env
        echo DB_PASSWORD=postgres >> .env
        
        echo ✅ LARAVEL INSTALLÉ MANUELLEMENT !
        echo 🌐 Démarrer avec: cd backend-laravel && php artisan serve
        
    ) else (
        echo ❌ Erreur extraction
    )
    
    del laravel.zip
    
) else (
    echo ❌ Erreur téléchargement Laravel
)

pause
