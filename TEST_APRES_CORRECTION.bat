@echo off
echo 🔧 TEST APRÈS CORRECTION SANCTUM
echo =================================

echo 📍 Arrêt processus PHP...
taskkill /F /IM php.exe 2>nul

echo 📍 Nettoyage cache Laravel...
cd backend
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear
C:\wamp64\bin\php\php8.2.0\php.exe artisan cache:clear

echo 📍 Test modèle User...
C:\wamp64\bin\php\php8.2.0\php.exe artisan tinker --execute="echo 'User model: '; $user = new App\Models\User(); echo 'OK';"

echo 📍 Démarrage Laravel...
start "Laravel Fixed" cmd /k "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 3 secondes...
timeout /t 3 /nobreak >nul

echo 📍 Test API login...
powershell -Command "$body = @{ email='franco@gmail.com'; password='I love teko.' } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/login' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 5; Write-Host 'LOGIN SUCCESS:'; $response | ConvertTo-Json -Depth 2 } catch { Write-Host 'LOGIN ERROR:' $_.Exception.Message }"

echo.
echo 🎯 MAINTENANT testez le frontend sur http://localhost:5174
echo.

pause
