@echo off
echo 🔍 DEBUG LOCALSTORAGE
echo ====================

echo 📍 Pour débugger le localStorage:
echo.
echo 1. Ouvrez les DevTools (F12)
echo 2. Onglet Console
echo 3. Tapez ces commandes:
echo.
echo    // Voir les produits
echo    console.log('Produits:', JSON.parse(localStorage.getItem('smarterp_products') ^|^| '{}'))
echo.
echo    // Voir les ventes  
echo    console.log('Ventes:', JSON.parse(localStorage.getItem('smarterp_sales') ^|^| '{}'))
echo.
echo    // Voir l'utilisateur actuel
echo    console.log('User:', JSON.parse(localStorage.getItem('smarterp_current_user') ^|^| 'null'))
echo.
echo 4. Vérifiez que les données sont bien sauvées par email
echo 5. Cliquez sur "🔄 Actualiser" dans le dashboard
echo.

pause
