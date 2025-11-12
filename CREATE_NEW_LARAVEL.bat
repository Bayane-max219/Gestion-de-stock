@echo off
echo 🚀 CRÉATION NOUVEAU LARAVEL PROPRE
echo ==================================

echo 📍 Création dossier backend-new...
mkdir backend-new
cd backend-new

echo 📍 Création structure Laravel minimale...
mkdir app\Http\Controllers\Api
mkdir config
mkdir routes
mkdir storage\logs
mkdir storage\framework\cache\data
mkdir storage\framework\sessions
mkdir storage\framework\views

echo 📍 Création .env...
echo APP_NAME="SmartERP Pro" > .env
echo APP_ENV=local >> .env
echo APP_KEY=base64:test123456789 >> .env
echo APP_DEBUG=true >> .env
echo APP_URL=http://localhost:8000 >> .env
echo. >> .env
echo DB_CONNECTION=mysql >> .env
echo DB_HOST=127.0.0.1 >> .env
echo DB_PORT=3306 >> .env
echo DB_DATABASE=stock_management >> .env
echo DB_USERNAME=root >> .env
echo DB_PASSWORD= >> .env

echo 📍 Création index.php...
echo ^<?php > public\index.php
echo require_once __DIR__ . '/../routes/web.php'; >> public\index.php

echo 📍 Création routes web.php...
echo ^<?php > routes\web.php
echo use Illuminate\Http\Request; >> routes\web.php
echo. >> routes\web.php
echo // Route test >> routes\web.php
echo if ^($_SERVER['REQUEST_URI'] == '/api/health'^) { >> routes\web.php
echo     header^('Content-Type: application/json'^); >> routes\web.php
echo     header^('Access-Control-Allow-Origin: *'^); >> routes\web.php
echo     echo json_encode^(['status' =^> 'OK', 'message' =^> 'New Laravel Works!']^); >> routes\web.php
echo     exit; >> routes\web.php
echo } >> routes\web.php

echo ✅ Nouveau Laravel créé !
echo 📍 Testez avec: cd backend-new && php -S localhost:8000 -t public
echo.

pause
