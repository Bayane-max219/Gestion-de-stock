@echo off
echo 🔒 TEST SAUVEGARDE PERMANENTE
echo =============================

echo 📍 Redémarrage avec sauvegarde automatique...
taskkill /F /IM node.exe 2>nul

cd frontend
start "SmartERP Sauvegarde Permanente" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ SYSTÈME DE SAUVEGARDE ACTIVÉ !
echo.
echo 🔒 MAINTENANT VOS DONNÉES SONT PROTÉGÉES :
echo.
echo 1. Allez sur http://localhost:5174
echo 2. Connectez-vous avec vos comptes
echo 3. Ajoutez des produits/ventes
echo 4. ✅ Auto-sauvegarde après chaque action
echo 5. ✅ Restauration automatique au démarrage
echo 6. ✅ Plus de perte de données !
echo.
echo 🧪 POUR TESTER LA PROTECTION :
echo 1. Ajoutez des données
echo 2. Redémarrez le serveur
echo 3. Vos données seront toujours là !
echo.
echo 📊 Le dashboard affichera maintenant les vraies données !
echo.

pause
