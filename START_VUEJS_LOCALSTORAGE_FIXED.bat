@echo off
echo 🔧 DÉMARRAGE VUE.JS LOCALSTORAGE CORRIGÉ
echo ========================================

echo 📍 Arrêt processus...
taskkill /F /IM node.exe 2>nul
taskkill /F /IM php.exe 2>nul

echo 📍 Nettoyage cache navigateur recommandé...
echo Appuyez sur Ctrl+Shift+R dans le navigateur pour vider le cache

echo 📍 Démarrage Vue.js avec fallback localStorage...
cd frontend
start "SmartERP Vue.js LocalStorage" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ VUE.JS LOCALSTORAGE CORRIGÉ !
echo.
echo 🎯 Maintenant testez :
echo 1. Allez sur http://localhost:5174
echo 2. Appuyez sur Ctrl+Shift+R pour vider le cache
echo 3. Connectez-vous avec franco@gmail.com / I love teko.
echo 4. Ajoutez des produits - ils seront sauvés en localStorage
echo 5. Dashboard sera cohérent avec les vrais produits
echo.
echo 📸 Prêt pour screenshots de la version fonctionnelle !
echo.

pause
