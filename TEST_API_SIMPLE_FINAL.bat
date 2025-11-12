@echo off
echo 🧪 TEST API SIMPLE FINAL
echo ========================

echo 📍 Test inscription utilisateur...
echo.

powershell -Command "$body = @{ firstName='Miguel'; lastName='Test'; email='miguel.final@smarterp.com'; password='password123'; confirmPassword='password123'; businessName='Boutique Final'; businessType='epicerie'; acceptTerms=$true } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://localhost:8002/register' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 10; Write-Host 'INSCRIPTION SUCCESS:'; $response | ConvertTo-Json -Depth 3 } catch { Write-Host 'INSCRIPTION ERROR:' $_.Exception.Message }"

echo.
echo 📍 Test connexion utilisateur...
echo.

powershell -Command "$body = @{ email='miguel.final@smarterp.com'; password='password123' } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://localhost:8002/login' -Method Post -ContentType 'application/json' -Body $body -TimeoutSec 10; Write-Host 'LOGIN SUCCESS:'; $response | ConvertTo-Json -Depth 3 } catch { Write-Host 'LOGIN ERROR:' $_.Exception.Message }"

echo.
echo 📍 Vérification MySQL...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT COUNT(*) as total_users FROM users; SELECT id, first_name, last_name, email, business_name FROM users ORDER BY id DESC LIMIT 3;" 2>nul

echo.
echo 🎯 Si tout fonctionne, changez l'URL API frontend vers:
echo    http://localhost:8002
echo.

pause
