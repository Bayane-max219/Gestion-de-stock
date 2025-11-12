@echo off
echo 🧪 TEST SIMPLE AVEC CURL
echo ========================

echo 📍 Test health API...
curl -s "http://127.0.0.1:8000/api/health"

echo.
echo.
echo 📍 Test login Franco...
curl -s -X POST "http://127.0.0.1:8000/api/login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"franco@gmail.com\",\"password\":\"I love teko.\"}"

echo.
echo.
echo 📍 Test page d'accueil...
curl -s "http://127.0.0.1:8000/"

echo.
echo.
pause
