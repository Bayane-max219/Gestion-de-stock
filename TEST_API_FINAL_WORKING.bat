@echo off
echo 🎯 TEST API FINAL WORKING
echo =========================

echo 📍 Test health API...
powershell -Command "try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/health' -TimeoutSec 3; Write-Host 'Health API: ✅ SUCCESS'; $response | ConvertTo-Json } catch { Write-Host 'Health API: ❌ ERROR -' $_.Exception.Message }"

echo.
echo 📍 Test login API...
powershell -Command "$body = @{ email='franco@gmail.com'; password='test' } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/login' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 5; Write-Host 'Login API: ✅ SUCCESS'; $response | ConvertTo-Json -Depth 2 } catch { Write-Host 'Login API: ❌ ERROR -' $_.Exception.Message }"

echo.
echo 📍 Test dashboard API...
powershell -Command "try { $response = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/dashboard' -TimeoutSec 3; Write-Host 'Dashboard API: ✅ SUCCESS'; $response | ConvertTo-Json -Depth 2 } catch { Write-Host 'Dashboard API: ❌ ERROR -' $_.Exception.Message }"

echo.
echo 🎯 Si tous les tests passent:
echo 1. Allez sur http://localhost:5174
echo 2. Connectez-vous avec franco@gmail.com / n'importe quel mot de passe
echo 3. L'application devrait maintenant fonctionner !
echo.

pause
