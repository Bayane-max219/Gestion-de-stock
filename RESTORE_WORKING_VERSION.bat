@echo off
echo 🔄 RESTAURATION VERSION FONCTIONNELLE
echo =====================================

echo 📍 Arrêt tous processus...
taskkill /F /IM node.exe 2>nul
taskkill /F /IM php.exe 2>nul

echo 📍 NETTOYAGE COMPLET localStorage...
echo.
echo ⚠️  IMPORTANT: Ouvrez le navigateur et faites:
echo 1. F12 (DevTools)
echo 2. Console
echo 3. Tapez: localStorage.clear()
echo 4. Appuyez sur Entrée
echo 5. Fermez et rouvrez le navigateur
echo.

echo 📍 Redémarrage Vue.js propre...
cd frontend
start "SmartERP Fonctionnel" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ RESTAURATION TERMINÉE !
echo.
echo 🎯 MAINTENANT:
echo 1. Nettoyez localStorage (voir instructions ci-dessus)
echo 2. Allez sur http://localhost:5174
echo 3. Connectez-vous avec franco@gmail.com / I love teko.
echo 4. Ajoutez 2-3 produits
echo 5. Faites 1-2 ventes
echo 6. Le dashboard DOIT se mettre à jour automatiquement
echo.
echo Si ça ne marche pas, il y a un bug dans le code qu'on va corriger !
echo.

pause
