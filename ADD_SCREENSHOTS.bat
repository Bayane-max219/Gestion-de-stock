@echo off
echo 📸 AJOUT DES SCREENSHOTS AU PROJET
echo ===================================

echo 📍 Création du dossier screenshots...
if not exist screenshots (
    mkdir screenshots
    echo ✅ Dossier screenshots créé
)

echo 📍 Instructions pour ajouter les screenshots:
echo.
echo 1. Prenez des captures d'écran de votre application:
echo    - Dashboard principal
echo    - Page de gestion de stock  
echo    - Interface de vente
echo    - Page des rapports
echo.
echo 2. Sauvegardez-les dans le dossier 'screenshots' avec ces noms:
echo    - dashboard.png
echo    - stock-management.png
echo    - sales.png
echo    - reports.png
echo.
echo 3. Puis exécutez ces commandes:
echo.
echo    git add screenshots/
echo    git commit -m "Add application screenshots"
echo    git push origin main
echo.
echo 📁 Le dossier screenshots est prêt !
echo.

pause
