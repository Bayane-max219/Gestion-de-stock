@echo off
echo 🏗️ CRÉATION BACKEND LARAVEL COMPLET
echo ===================================

set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe
set PROJECT_DIR=%~dp0

cd /d "%PROJECT_DIR%"

echo 📁 Création du projet Laravel...
%PHP_PATH% composer.phar create-project laravel/laravel backend-laravel

cd backend-laravel

echo 📦 Installation des packages API...
%PHP_PATH% ..\composer.phar require laravel/sanctum
%PHP_PATH% ..\composer.phar require fruitcake/laravel-cors

echo 🔧 Configuration .env pour PostgreSQL...
copy .env.example .env

echo 🔑 Génération de la clé d'application...
%PHP_PATH% artisan key:generate

echo 📊 Publication Sanctum...
%PHP_PATH% artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo 🗃️ Création des migrations...
%PHP_PATH% artisan make:migration create_products_table
%PHP_PATH% artisan make:migration create_categories_table
%PHP_PATH% artisan make:migration create_customers_table
%PHP_PATH% artisan make:migration create_sales_table
%PHP_PATH% artisan make:migration create_sale_items_table

echo 🎯 Création des modèles...
%PHP_PATH% artisan make:model Product
%PHP_PATH% artisan make:model Category
%PHP_PATH% artisan make:model Customer
%PHP_PATH% artisan make:model Sale
%PHP_PATH% artisan make:model SaleItem

echo 🌐 Création des contrôleurs API...
%PHP_PATH% artisan make:controller Api\AuthController
%PHP_PATH% artisan make:controller Api\ProductController --api
%PHP_PATH% artisan make:controller Api\CategoryController --api
%PHP_PATH% artisan make:controller Api\CustomerController --api
%PHP_PATH% artisan make:controller Api\SaleController --api
%PHP_PATH% artisan make:controller Api\ReportController
%PHP_PATH% artisan make:controller Api\DashboardController

echo ✅ Backend Laravel créé avec succès !
echo 🔗 Structure complète:
echo    - API REST avec Sanctum
echo    - Modèles + Migrations
echo    - Contrôleurs API
echo    - Configuration PostgreSQL

pause
