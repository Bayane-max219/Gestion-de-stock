@echo off
echo 🚀 CRÉATION DU BACKEND LARAVEL
echo ================================

cd "C:\Users\Miguel\Desktop\Applikcation Octobre\Projet gestion de stock"

echo 📁 Création du projet Laravel...
composer create-project laravel/laravel backend-laravel

cd backend-laravel

echo 📦 Installation des packages nécessaires...
composer require laravel/sanctum
composer require fruitcake/laravel-cors

echo 🔧 Configuration de Sanctum (authentification API)...
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo 📊 Création des migrations pour PostgreSQL...
php artisan make:migration create_products_table
php artisan make:migration create_categories_table  
php artisan make:migration create_customers_table
php artisan make:migration create_sales_table
php artisan make:migration create_sale_items_table

echo 🎯 Création des modèles...
php artisan make:model Product
php artisan make:model Category
php artisan make:model Customer  
php artisan make:model Sale
php artisan make:model SaleItem

echo 🌐 Création des contrôleurs API...
php artisan make:controller Api\AuthController
php artisan make:controller Api\ProductController --api
php artisan make:controller Api\CategoryController --api
php artisan make:controller Api\CustomerController --api
php artisan make:controller Api\SaleController --api
php artisan make:controller Api\ReportController
php artisan make:controller Api\DashboardController

echo ✅ Backend Laravel créé avec succès !
echo 📝 Prochaine étape : Configurer PostgreSQL dans .env

pause
