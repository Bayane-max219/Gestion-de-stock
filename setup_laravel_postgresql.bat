@echo off
echo 🚀 SETUP BACKEND LARAVEL AVEC POSTGRESQL
echo =========================================

cd "C:\Users\Miguel\Desktop\Applikcation Octobre\Projet gestion de stock"

echo 📁 Création du projet Laravel...
composer create-project laravel/laravel backend-laravel

cd backend-laravel

echo 📦 Installation des packages...
composer require laravel/sanctum fruitcake/laravel-cors

echo 🔧 Configuration PostgreSQL dans .env...
powershell -Command "(gc .env) -replace 'DB_CONNECTION=mysql', 'DB_CONNECTION=pgsql' | Out-File -encoding ASCII .env.tmp"
powershell -Command "(gc .env.tmp) -replace 'DB_HOST=.*', 'DB_HOST=127.0.0.1' | Out-File -encoding ASCII .env.tmp2"
powershell -Command "(gc .env.tmp2) -replace 'DB_PORT=.*', 'DB_PORT=5432' | Out-File -encoding ASCII .env.tmp3"
powershell -Command "(gc .env.tmp3) -replace 'DB_DATABASE=.*', 'DB_DATABASE=smarterp_pro' | Out-File -encoding ASCII .env.tmp4"
powershell -Command "(gc .env.tmp4) -replace 'DB_USERNAME=.*', 'DB_USERNAME=postgres' | Out-File -encoding ASCII .env.tmp5"
powershell -Command "(gc .env.tmp5) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=postgres' | Out-File -encoding ASCII .env"
del .env.tmp .env.tmp2 .env.tmp3 .env.tmp4 .env.tmp5

echo 🔑 Génération de la clé...
php artisan key:generate

echo 📊 Configuration Sanctum...
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo ✅ Backend Laravel configuré pour PostgreSQL !
echo 🔗 Base : postgresql://postgres:postgres@localhost:5432/smarterp_pro

pause
