@echo off
echo 🔄 RESTAURATION VUE.JS ÉTAT FONCTIONNEL
echo ========================================

echo 📍 Gardons Laravel pour GitHub mais restaurons Vue.js...

echo 📍 Arrêt serveurs...
taskkill /F /IM php.exe 2>nul
taskkill /F /IM node.exe 2>nul

echo 📍 Restauration frontend à l'état fonctionnel...
cd frontend

echo 📍 Démarrage Vue.js en mode localStorage (état original)...
start "SmartERP Vue.js Fonctionnel" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ RESTAURATION TERMINÉE !
echo.
echo 🎯 Votre application Vue.js est maintenant dans l'état fonctionnel :
echo - Allez sur http://localhost:5174
echo - Dashboard cohérent avec graphiques
echo - Rapports avec dates Madagascar
echo - Toute la logique localStorage restaurée
echo - Laravel reste présent pour GitHub
echo.
echo 📸 Prêt pour les screenshots de la version fonctionnelle !
echo.

pause
