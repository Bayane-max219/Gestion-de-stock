@echo off
echo 🔧 TEST FINAL FORCÉ
echo ==================

echo 📍 Arrêt complet...
taskkill /F /IM php.exe 2>nul
timeout /t 2 /nobreak >nul

echo 📍 Vérification index minimal...
cd backend\public
if exist index_minimal.php (
    del index.php 2>nul
    copy index_minimal.php index.php
    echo ✅ Index minimal installé
) else (
    echo ❌ Index minimal manquant
)

echo 📍 Redémarrage serveur...
cd ..
start "Test Final" cmd /k "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo 📍 Test direct avec PowerShell...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/api/health' -TimeoutSec 5; Write-Host 'Health: ✅' $response.Content } catch { Write-Host 'Health: ❌' $_.Exception.Message }"

echo.
echo 📍 Test login avec PowerShell...
powershell -Command "$body = '{\"email\":\"franco@gmail.com\",\"password\":\"I love teko.\"}'; try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/api/login' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 5; Write-Host 'Login: ✅' $response.Content } catch { Write-Host 'Login: ❌' $_.Exception.Message }"

echo.
echo 🎯 Si les tests passent, testez le frontend !
pause
