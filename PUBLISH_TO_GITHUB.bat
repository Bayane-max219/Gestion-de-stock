@echo off
echo 🚀 PUBLICATION PROJET SMARTERP SUR GITHUB
echo =========================================

echo 📍 Étape 1: Nettoyage ancien Git...
if exist .git (
    rmdir /s /q .git
    echo ✅ Ancien .git supprimé
) else (
    echo ℹ️  Pas d'ancien .git trouvé
)

echo 📍 Étape 2: Initialisation nouveau Git...
git init
git config user.name "Bayane-max215"
git config user.email "baymi312@gmail.com"

echo 📍 Étape 3: Création dossier screenshots...
if not exist screenshots (
    mkdir screenshots
    echo ✅ Dossier screenshots créé
)

echo 📍 Étape 4: Ajout de tous les fichiers...
git add .

echo 📍 Étape 5: Premier commit...
git commit -m "Initial commit - SmartERP Vue.js + Laravel Stock Management System"

echo 📍 Étape 6: Création branche main...
git branch -M main

echo 📍 Étape 7: Ajout remote GitHub...
git remote add origin https://github.com/Bayane-max219/Gestion-de-stock.git

echo 📍 Étape 8: Push vers GitHub...
git push -f origin main

echo ✅ PUBLICATION TERMINÉE !
echo.
echo 📸 N'oubliez pas d'ajouter vos screenshots dans le dossier 'screenshots'
echo 📝 Puis faites: git add screenshots/ && git commit -m "Add screenshots" && git push
echo.
echo 🌐 Votre projet est maintenant sur: https://github.com/Bayane-max219/Gestion-de-stock
echo.

pause
