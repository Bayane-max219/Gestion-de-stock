@echo off
echo 🧪 TEST INSCRIPTION SIMPLE
echo =========================

echo 📍 Test de l'endpoint /api/register...
echo.

curl -X POST "http://localhost:8000/api/register" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"firstName\":\"Test\",\"lastName\":\"User\",\"email\":\"test@example.com\",\"password\":\"password123\",\"confirmPassword\":\"password123\",\"businessName\":\"Test Business\",\"businessType\":\"epicerie\",\"acceptTerms\":true}"

echo.
echo.
echo 📍 Vérification en base MySQL...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT id, first_name, last_name, email, business_name FROM users ORDER BY id DESC LIMIT 1;"

echo.
pause
