@echo off
echo 🧹 REDÉMARRAGE AVEC LOCALSTORAGE CORRIGÉ
echo ========================================

echo 📍 Arrêt processus...
taskkill /F /IM node.exe 2>nul

echo 📍 IMPORTANT: Nettoyez le localStorage dans le navigateur :
echo 1. Ouvrez les DevTools (F12)
echo 2. Onglet Application/Storage
echo 3. Local Storage > http://localhost:5174
echo 4. Supprimez smarterp_products et smarterp_sales
echo 5. OU utilisez: localStorage.clear()

echo 📍 Redémarrage frontend...
cd frontend
start "SmartERP LocalStorage Fixed" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ REDÉMARRAGE TERMINÉ !
echo.
echo 🎯 MAINTENANT TESTEZ :
echo 1. Allez sur http://localhost:5174
echo 2. Nettoyez le localStorage (voir instructions ci-dessus)
echo 3. Rechargez la page (F5)
echo 4. Connectez-vous avec franco@gmail.com
echo 5. Ajoutez des produits
echo 6. Faites des ventes
echo 7. Le dashboard se mettra à jour automatiquement !
echo.

pause
