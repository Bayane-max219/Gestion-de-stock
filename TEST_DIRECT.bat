@echo off
echo 🧪 TEST DIRECT API LARAVEL
echo ==========================

echo 📍 Test depuis le dossier racine...
echo.

echo 📍 1. Vérification serveur Laravel...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:8001' -TimeoutSec 5; Write-Host 'Serveur Laravel: OK' } catch { Write-Host 'Serveur Laravel: ERREUR -' $_.Exception.Message }"

echo.
echo 📍 2. Test inscription API...
powershell -Command "$body = @{ firstName='Miguel'; lastName='Test'; email='miguel.test@smarterp.com'; password='password123'; confirmPassword='password123'; businessName='Boutique Test'; businessType='epicerie'; acceptTerms=$true } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://localhost:8001/api/register' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 10; Write-Host 'API SUCCESS:'; $response | ConvertTo-Json -Depth 3 } catch { Write-Host 'API ERROR:' $_.Exception.Message; if ($_.Exception.Response) { $_.Exception.Response.StatusCode } }"

echo.
echo 📍 3. Vérification MySQL...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT COUNT(*) as total_users FROM users; SELECT id, first_name, last_name, email FROM users ORDER BY id DESC LIMIT 3;" 2>nul

echo.
echo 🎯 Si l'API fonctionne, démarrez le frontend:
echo    cd frontend
echo    npm run dev
echo.

pause
