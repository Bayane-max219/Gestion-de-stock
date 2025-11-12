@echo off
echo 🧪 TEST COMPTES PAR DÉFAUT
echo ==========================

echo 📍 Redémarrage serveur propre...
taskkill /F /IM node.exe 2>nul

cd frontend
start "SmartERP Test Comptes" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ SERVEUR REDÉMARRÉ !
echo.
echo 🧪 TESTEZ CES COMPTES EXACTEMENT :
echo.
echo 1. Franco:
echo    Email: franco@gmail.com
echo    Mot de passe: I love teko.
echo.
echo 2. Fatima:
echo    Email: fatima@gmail.com  
echo    Mot de passe: quincaillerie
echo.
echo ⚠️  ATTENTION: Respectez exactement les majuscules/minuscules !
echo.
echo 🔍 Si erreur, ouvrez F12 et regardez la console
echo.

pause
