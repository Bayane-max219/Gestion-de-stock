@echo off
echo 🧹 NETTOYAGE STRUCTURE LARAVEL
echo ==============================

cd backend

echo 📍 Suppression fichiers problématiques...
del /Q routes\api_*.php 2>nul
del /Q app\Http\Controllers\Api\AuthControllerSimple.php 2>nul

echo 📍 Sauvegarde fichiers importants...
copy routes\api.php routes\api_backup.php
copy app\Http\Controllers\Api\AuthController.php app\Http\Controllers\Api\AuthController_backup.php

echo 📍 Nettoyage cache complet...
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear 2>nul
C:\wamp64\bin\php\php8.2.0\php.exe artisan cache:clear 2>nul
C:\wamp64\bin\php\php8.2.0\php.exe artisan route:clear 2>nul
C:\wamp64\bin\php\php8.2.0\php.exe artisan view:clear 2>nul

echo 📍 Suppression logs d'erreur...
del /Q storage\logs\*.log 2>nul

echo ✅ Nettoyage terminé !
echo 📍 Prêt pour reconstruction propre...

pause
