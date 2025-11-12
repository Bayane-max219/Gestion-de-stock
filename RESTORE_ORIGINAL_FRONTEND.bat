@echo off
echo 🔄 RESTAURATION FRONTEND ORIGINAL
echo =================================

echo 📍 Arrêt serveur Laravel...
taskkill /F /IM php.exe 2>nul

echo 📍 Sauvegarde version Laravel...
if exist backend_laravel_attempt (
    rmdir /s /q backend_laravel_attempt
)
move backend backend_laravel_attempt

echo 📍 Le frontend Vue.js va maintenant utiliser localStorage uniquement
echo 📍 Plus besoin de Laravel - tout fonctionne côté client

echo 📍 Démarrage frontend Vue.js...
cd frontend
start "Vue.js Original" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ RESTAURATION TERMINÉE !
echo.
echo 🎯 Votre application Vue.js originale est maintenant active :
echo - Allez sur http://localhost:5174
echo - Toutes les fonctionnalités localStorage sont restaurées
echo - Rapports, ventes, stock, dashboard - tout fonctionne !
echo - Dates Madagascar, cohérence parfaite
echo.

pause
