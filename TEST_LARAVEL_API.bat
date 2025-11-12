@echo off
echo 🧪 TEST API LARAVEL
echo ===================

echo 📍 Test connexion Laravel...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:8000' -TimeoutSec 5; Write-Host 'Laravel: ✅ CONNECTÉ' } catch { Write-Host 'Laravel: ❌ ERREUR -' $_.Exception.Message }"

echo.
echo 📍 Test route API register...
powershell -Command "$body = @{ firstName='TestLaravel'; lastName='User'; email='test.laravel@smarterp.com'; password='password123'; confirmPassword='password123'; businessName='Test Laravel'; businessType='epicerie'; acceptTerms=$true } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://localhost:8000/api/register' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 10; Write-Host 'API Register: ✅ SUCCESS'; $response | ConvertTo-Json -Depth 2 } catch { Write-Host 'API Register: ❌ ERROR -' $_.Exception.Message }"

echo.
echo 📍 Vérification MySQL...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT COUNT(*) as total_users FROM users; SELECT id, first_name, last_name, email FROM users ORDER BY id DESC LIMIT 3;" 2>nul

echo.
pause
