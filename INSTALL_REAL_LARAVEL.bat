@echo off
echo 🚀 INSTALLATION LARAVEL RÉEL + POSTGRESQL
echo ==========================================

set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe
set PROJECT_DIR=%~dp0

echo 📁 Suppression de l'ancien backend simulé...
if exist "backend-laravel-real" rmdir /s /q "backend-laravel-real"

echo 📥 Téléchargement de Composer...
%PHP_PATH% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
%PHP_PATH% composer-setup.php
del composer-setup.php

echo 🏗️ Création du projet Laravel réel...
%PHP_PATH% composer.phar create-project laravel/laravel backend-laravel-real

cd backend-laravel-real

echo 📦 Installation des packages API...
%PHP_PATH% ..\composer.phar require laravel/sanctum
%PHP_PATH% ..\composer.phar require fruitcake/laravel-cors

echo 🔧 Configuration .env pour PostgreSQL...
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

echo 🔑 Génération de la clé d'application...
%PHP_PATH% artisan key:generate

echo 📊 Publication Sanctum...
%PHP_PATH% artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo ✅ Laravel réel installé !
echo 🔗 Prochaine étape: Créer les migrations et modèles

pause
