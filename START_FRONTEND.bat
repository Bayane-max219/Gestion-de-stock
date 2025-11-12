@echo off
echo 🌐 DÉMARRAGE FRONTEND VUE.JS
echo =============================

echo 📁 Dossier actuel: %cd%

echo.
echo 🔧 Vérification Node.js...
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Node.js non trouvé
    echo 💡 Installez Node.js depuis: https://nodejs.org/
    pause
    exit /b 1
)

echo ✅ Node.js trouvé
node --version

echo.
echo 📁 Accès au dossier frontend...
if not exist "frontend" (
    echo ❌ Dossier frontend non trouvé
    pause
    exit /b 1
)

cd frontend

echo.
echo 📦 Vérification des dépendances...
if not exist "node_modules" (
    echo 🔄 Installation des dépendances...
    npm install
)

echo.
echo 🚀 Démarrage du serveur de développement...
echo 🌐 Application disponible sur: http://localhost:5173
echo.

npm run dev

pause
