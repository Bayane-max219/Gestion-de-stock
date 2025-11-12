@echo off
echo 🔄 RECRÉATION DES COMPTES PERDUS
echo ================================

echo 📍 Redémarrage serveur...
taskkill /F /IM node.exe 2>nul
cd frontend
start "SmartERP Comptes Fixes" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ SERVEUR REDÉMARRÉ !
echo.
echo 🎯 MAINTENANT RECRÉEZ VOS 4 COMPTES :
echo.
echo 1. Allez sur http://localhost:5174
echo 2. Cliquez sur "Créer un compte"
echo 3. Recréez vos 4 comptes un par un
echo 4. Ils seront maintenant SAUVÉS DÉFINITIVEMENT en localStorage
echo 5. Même après redémarrage, ils resteront disponibles !
echo.
echo 📝 Comptes par défaut disponibles :
echo - franco@gmail.com / I love teko.
echo - fatima@gmail.com / quincaillerie
echo.
echo 🔒 VOS NOUVEAUX COMPTES SERONT PERSISTANTS !
echo.

pause
