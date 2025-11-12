@echo off
echo 🔒 SAUVEGARDE COMPLÈTE DES DONNÉES
echo ==================================

echo 📍 Pour sauvegarder et vérifier vos 4 comptes:
echo.
echo 1. Ouvrez http://localhost:5174
echo 2. F12 > Console
echo 3. Copiez-collez ce script complet:
echo.
echo // SCRIPT DE SAUVEGARDE COMPLÈTE
echo console.log('=== VÉRIFICATION DONNÉES ===');
echo console.log('Comptes:', JSON.parse(localStorage.getItem('smarterp_accounts') ^|^| '[]'));
echo console.log('Produits:', JSON.parse(localStorage.getItem('smarterp_products') ^|^| '{}'));
echo console.log('Ventes:', JSON.parse(localStorage.getItem('smarterp_sales') ^|^| '{}'));
echo console.log('User actuel:', JSON.parse(localStorage.getItem('smarterp_current_user') ^|^| 'null'));
echo.
echo // CRÉER BACKUP PERMANENT
echo const backup = {
echo   accounts: JSON.parse(localStorage.getItem('smarterp_accounts') ^|^| '[]'),
echo   products: JSON.parse(localStorage.getItem('smarterp_products') ^|^| '{}'),
echo   sales: JSON.parse(localStorage.getItem('smarterp_sales') ^|^| '{}'),
echo   timestamp: new Date().toISOString()
echo };
echo localStorage.setItem('smarterp_backup', JSON.stringify(backup));
echo console.log('✅ BACKUP CRÉÉ:', backup);
echo.
echo 4. Copiez-moi le résultat pour voir vos données !
echo.

pause
