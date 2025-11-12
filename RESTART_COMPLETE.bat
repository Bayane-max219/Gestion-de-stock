@echo off
echo 🔄 REDÉMARRAGE COMPLET LARAVEL
echo ==============================

echo 📍 Arrêt des processus PHP...
taskkill /F /IM php.exe 2>nul

echo 📍 Nettoyage cache Laravel...
cd backend
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear
C:\wamp64\bin\php\php8.2.0\php.exe artisan cache:clear
C:\wamp64\bin\php\php8.2.0\php.exe artisan route:clear

echo 📍 Vérification des routes...
C:\wamp64\bin\php\php8.2.0\php.exe artisan route:list

echo.
echo 📍 Redémarrage Laravel...
C:\wamp64\bin\php\php8.2.0\php.exe artisan serve

pause
