@echo off
echo 🧪 TEST API LARAVEL DIRECT
echo ==========================

echo 📍 Test page d'accueil Laravel...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:8000' -TimeoutSec 5; Write-Host 'Laravel Home: ✅ Status' $response.StatusCode } catch { Write-Host 'Laravel Home: ❌ ERROR -' $_.Exception.Message }"

echo.
echo 📍 Test route API register...
powershell -Command "$body = @{ firstName='TestDirect'; lastName='User'; email='test.direct@smarterp.com'; password='password123'; confirmPassword='password123'; businessName='Test Direct'; businessType='epicerie'; acceptTerms=$true } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/register' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 10; Write-Host 'API Register: ✅ SUCCESS'; $response | ConvertTo-Json -Depth 2 } catch { Write-Host 'API Register: ❌ ERROR -' $_.Exception.Message; Write-Host 'Status:' $_.Exception.Response.StatusCode }"

echo.
echo 📍 Test route API login...
powershell -Command "$body = @{ email='test.direct@smarterp.com'; password='password123' } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/login' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 10; Write-Host 'API Login: ✅ SUCCESS'; $response | ConvertTo-Json -Depth 2 } catch { Write-Host 'API Login: ❌ ERROR -' $_.Exception.Message }"

pause
