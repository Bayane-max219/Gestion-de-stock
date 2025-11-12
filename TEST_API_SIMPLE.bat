@echo off
echo 🧪 TEST API SIMPLE
echo ==================

echo 📍 Test inscription utilisateur...
echo.

powershell -Command "$body = @{ firstName='Test'; lastName='User'; email='test@example.com'; password='password123'; confirmPassword='password123'; businessName='Test Business'; businessType='epicerie'; acceptTerms=$true } | ConvertTo-Json; try { $response = Invoke-RestMethod -Uri 'http://localhost:8001/api/register' -Method Post -ContentType 'application/json' -Body $body; Write-Host 'SUCCESS:'; $response | ConvertTo-Json } catch { Write-Host 'ERROR:' $_.Exception.Message }"

echo.
echo 📍 Vérification en base MySQL...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT id, first_name, last_name, email, business_name FROM users ORDER BY id DESC LIMIT 1;"

pause
