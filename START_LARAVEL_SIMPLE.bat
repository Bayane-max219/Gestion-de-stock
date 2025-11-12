@echo off
echo 🚀 DÉMARRAGE LARAVEL SIMPLE
echo ===========================

cd backend

echo 📍 Nettoyage cache Laravel...
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear
C:\wamp64\bin\php\php8.2.0\php.exe artisan cache:clear

echo.
echo 📍 Démarrage serveur Laravel sur port 8000...
echo 🌐 URL: http://localhost:8000
echo 🛑 Appuyez sur Ctrl+C pour arrêter
echo.

C:\wamp64\bin\php\php8.2.0\php.exe artisan serve --port=8000

pause
