@echo off
echo 🎼 INSTALLATION COMPOSER
echo ========================

set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe

echo 📥 Téléchargement de Composer...
%PHP_PATH% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

echo 🔧 Installation de Composer...
%PHP_PATH% composer-setup.php

echo 🧹 Nettoyage...
%PHP_PATH% -r "unlink('composer-setup.php');"

echo ✅ Composer installé !
echo 🧪 Test Composer...
%PHP_PATH% composer.phar --version

echo 💡 Pour utiliser Composer globalement:
echo    php composer.phar [commande]

pause
