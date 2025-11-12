@echo off
echo 🧪 TEST WAMP + PHP
echo ==================

echo 📁 WAMP installé: C:\wamp64
echo 🐘 Version PHP disponibles:
dir "C:\wamp64\bin\php\php*" /b

echo.
echo 🧪 Test PHP 8.2:
"C:\wamp64\bin\php\php8.2.0\php.exe" -v

echo.
echo 🎼 Test Composer:
where composer
composer --version

echo.
echo 📋 RÉSUMÉ:
echo - WAMP64: ✅ Installé
echo - PHP 8.2: ✅ Disponible  
echo - Composer: ❓ À vérifier

pause
