@echo off
echo 🔍 DEBUG ERREUR 500 LARAVEL
echo ===========================

echo 📍 Test avec curl détaillé...
curl -v -X POST "http://127.0.0.1:8000/api/login" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"email\":\"franco@gmail.com\",\"password\":\"I love teko.\"}"

echo.
echo.
echo 📍 Test route health...
curl -v "http://127.0.0.1:8000/api/health"

echo.
echo.
pause
