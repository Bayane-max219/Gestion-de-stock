@echo off
echo 🔍 VÉRIFICATION DONNÉES FATIMA
echo ==============================

echo 📍 Pour vérifier les données de Fatima:
echo.
echo 1. Ouvrez http://localhost:5174
echo 2. Appuyez sur F12 (DevTools)
echo 3. Onglet Console
echo 4. Tapez ces commandes:
echo.
echo    // Voir les comptes sauvés
echo    console.log('Comptes:', JSON.parse(localStorage.getItem('smarterp_accounts') ^|^| '[]'))
echo.
echo    // Voir les produits de Fatima
echo    console.log('Produits:', JSON.parse(localStorage.getItem('smarterp_products') ^|^| '{}'))
echo.
echo    // Voir les ventes de Fatima  
echo    console.log('Ventes:', JSON.parse(localStorage.getItem('smarterp_sales') ^|^| '{}'))
echo.
echo    // Voir utilisateur actuel
echo    console.log('User actuel:', JSON.parse(localStorage.getItem('smarterp_current_user') ^|^| 'null'))
echo.
echo 5. Copiez-moi ce que vous voyez !
echo.

pause
