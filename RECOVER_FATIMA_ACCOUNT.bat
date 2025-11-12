@echo off
echo 🔄 RÉCUPÉRATION COMPTE FATIMA
echo =============================

echo 📍 Redémarrage serveur...
taskkill /F /IM node.exe 2>nul
cd frontend
start "SmartERP Fatima Recovery" cmd /k "npm run dev"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo ✅ SERVEUR REDÉMARRÉ !
echo.
echo 🔄 RÉCUPÉRATION DONNÉES FATIMA :
echo.
echo 1. Allez sur http://localhost:5174
echo 2. Essayez ces combinaisons pour Fatima:
echo.
echo    Option A:
echo    Email: fatima@gmail.com
echo    Mot de passe: quincaillerie
echo.
echo    Option B:
echo    Email: fatima@gmail.com  
echo    Mot de passe: fatima
echo.
echo    Option C:
echo    Email: fatima@gmail.com
echo    Mot de passe: (vide)
echo.
echo 3. Si aucune ne marche, créez un nouveau compte Fatima
echo 4. Vos données (produits/ventes) sont toujours là !
echo.

pause
