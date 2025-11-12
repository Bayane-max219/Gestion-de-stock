@echo off
echo 🧪 TEST RAPIDE LARAVEL
echo =====================

echo 📍 Test health...
powershell -Command "try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/health' -TimeoutSec 3; Write-Host 'Health: ✅' $response.message } catch { Write-Host 'Health: ❌' $_.Exception.Message }"

echo.
echo 📍 Test login franco...
powershell -Command "$body = @{ email='franco@gmail.com'; password='I love teko.' } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/login' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 5; Write-Host 'Login: ✅ SUCCESS'; $response | ConvertTo-Json -Depth 2 } catch { Write-Host 'Login: ❌ ERROR -' $_.Exception.Message }"

pause
