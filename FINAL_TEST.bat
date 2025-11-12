@echo off
echo 🎯 TEST FINAL SMARTERP PRO
echo =========================

echo 📍 1. Démarrage serveur Laravel sur port 8001...
start "Laravel Server" cmd /k "cd backend && C:\wamp64\bin\php\php8.2.0\php.exe artisan serve --port=8001"

echo 📍 2. Attente 5 secondes...
timeout /t 5 /nobreak >nul

echo 📍 3. Test API avec PowerShell...
powershell -Command "try { $response = Invoke-RestMethod -Uri 'http://localhost:8001/api/register' -Method Post -ContentType 'application/json' -Body '{\"firstName\":\"Test\",\"lastName\":\"User\",\"email\":\"test@example.com\",\"password\":\"password123\",\"confirmPassword\":\"password123\",\"businessName\":\"Test Business\",\"businessType\":\"epicerie\",\"acceptTerms\":true}'; Write-Host 'API Response:'; $response | ConvertTo-Json } catch { Write-Host 'Error:' $_.Exception.Message }"

echo.
echo 📍 4. Vérification en base MySQL...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT id, first_name, last_name, email, business_name FROM users ORDER BY id DESC LIMIT 1;"

echo.
echo 🎯 MAINTENANT:
echo 1. Changez l'URL API dans frontend/src/services/api.js vers http://localhost:8001/api
echo 2. Démarrez le frontend: cd frontend && npm run dev
echo 3. Testez l'inscription sur http://localhost:5173
echo.

pause
