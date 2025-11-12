@echo off
echo 🔍 DIAGNOSTIC LARAVEL
echo ====================

cd backend

echo 📍 Test PHP...
C:\wamp64\bin\php\php8.2.0\php.exe --version

echo.
echo 📍 Test Artisan...
C:\wamp64\bin\php\php8.2.0\php.exe artisan --version

echo.
echo 📍 Test configuration Laravel...
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear

echo.
echo 📍 Test connexion base de données...
C:\wamp64\bin\php\php8.2.0\php.exe artisan migrate:status

echo.
echo 📍 Test démarrage serveur (5 secondes)...
timeout /t 2 /nobreak >nul
start /min cmd /c "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve --port=8000 > laravel_output.log 2>&1"

echo 📍 Attente 3 secondes...
timeout /t 3 /nobreak >nul

echo 📍 Test connexion HTTP...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:8000' -TimeoutSec 3; Write-Host 'HTTP Status:' $response.StatusCode } catch { Write-Host 'HTTP Error:' $_.Exception.Message }"

echo.
echo 📍 Logs Laravel (si erreur):
if exist laravel_output.log (
    type laravel_output.log
) else (
    echo Aucun log trouvé
)

echo.
echo 📍 Arrêt du serveur test...
taskkill /F /IM php.exe 2>nul

pause
