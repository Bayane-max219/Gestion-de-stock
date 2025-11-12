@echo off
echo 🗄️ CONFIGURATION MYSQL POUR SMARTERP PRO
echo =======================================

echo 📍 Création de la base MySQL...

REM Utiliser MySQL de WAMP
set MYSQL_EXE="C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe"

if not exist %MYSQL_EXE% (
    echo ❌ MySQL WAMP non trouvé
    echo 💡 Vérifiez le chemin MySQL dans WAMP
    pause
    exit /b 1
)

echo ✅ MySQL WAMP trouvé: %MYSQL_EXE%
echo.
echo 🔧 Création de la base stock_management...
echo 💡 Mot de passe root MySQL requis (souvent vide dans WAMP)
echo.

%MYSQL_EXE% -u root -p -e "source create_mysql_database.sql"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ Base MySQL créée avec succès !
    echo 📊 Base: stock_management
    echo 👤 Utilisateur: root
    echo 🏠 Serveur: localhost:3306
    echo.
    echo 🚀 Prochaine étape: Migrations Laravel
    echo    cd backend
    echo    C:\wamp64\bin\php\php8.2.0\php.exe artisan migrate
) else (
    echo.
    echo ❌ Erreur lors de la création de la base
    echo 💡 Vérifiez que MySQL est démarré dans WAMP
    echo 💡 Vérifiez le mot de passe root
)

echo.
pause
