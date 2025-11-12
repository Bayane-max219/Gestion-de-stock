@echo off
echo 🎯 TEST FINAL SIMPLE
echo ===================

echo 📍 Arrêt processus PHP...
taskkill /F /IM php.exe 2>nul

echo 📍 Nettoyage cache...
cd backend
C:\wamp64\bin\php\php8.2.0\php.exe artisan config:clear
C:\wamp64\bin\php\php8.2.0\php.exe artisan route:clear

echo 📍 Démarrage Laravel...
start "Laravel API" cmd /k "C:\wamp64\bin\php\php8.2.0\php.exe artisan serve"

echo 📍 Attente 3 secondes...
timeout /t 3 /nobreak >nul

echo 📍 Test API login...
powershell -Command "$body = @{ email='franco@gmail.com'; password='I love teko.' } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/login' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 5; Write-Host 'LOGIN SUCCESS:'; $response | ConvertTo-Json -Depth 2 } catch { Write-Host 'LOGIN ERROR:' $_.Exception.Message }"

echo.
echo 🎯 Si le login fonctionne:
echo 1. Allez sur http://localhost:5174
echo 2. Connectez-vous avec franco@gmail.com / I love teko.
echo.

pause
