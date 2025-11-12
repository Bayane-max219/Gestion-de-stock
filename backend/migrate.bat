@echo off
echo 🚀 MIGRATION LARAVEL AVEC POSTGRESQL
echo ==================================

echo 📍 Recherche de PostgreSQL...

REM Chemins possibles PostgreSQL - PostgreSQL n'inclut pas PHP, utilisons les outils PostgreSQL
if exist "C:\Program Files\PostgreSQL\16\bin\psql.exe" (
    echo ✅ PostgreSQL 16 trouvé mais pas de PHP inclus
    echo 💡 PostgreSQL moderne n'inclut plus PHP
)

if exist "C:\Program Files\PostgreSQL\15\bin\php.exe" (
    set PHP_EXE="C:\Program Files\PostgreSQL\15\bin\php.exe"
    echo ✅ PostgreSQL 15 trouvé
    goto :migrate
)

if exist "C:\Program Files\PostgreSQL\14\bin\php.exe" (
    set PHP_EXE="C:\Program Files\PostgreSQL\14\bin\php.exe"
    echo ✅ PostgreSQL 14 trouvé
    goto :migrate
)

REM Fallback vers WAMP
if exist "C:\wamp64\bin\php\php8.2.0\php.exe" (
    set PHP_EXE="C:\wamp64\bin\php\php8.2.0\php.exe"
    echo ⚠️ Utilisation WAMP PHP (peut ne pas fonctionner avec PostgreSQL)
    goto :migrate
)

echo ❌ Aucun PHP trouvé
pause
exit /b 1

:migrate
echo 🔄 Exécution des migrations...
echo Utilisation de: %PHP_EXE%
echo.

%PHP_EXE% artisan migrate

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ MIGRATIONS RÉUSSIES !
    echo 🗄️ Tables créées dans PostgreSQL
    echo.
    echo 🚀 Voulez-vous démarrer le serveur ? (O/N)
    set /p choice=
    if /i "%choice%"=="O" (
        echo Démarrage du serveur Laravel...
        %PHP_EXE% artisan serve
    )
) else (
    echo.
    echo ❌ Erreur lors des migrations
    echo 💡 Vérifiez que PostgreSQL est démarré
    echo 💡 Vérifiez la configuration .env
)

pause
