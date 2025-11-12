@echo off
echo 🔍 DEBUG LOGIN API
echo ==================

echo 📍 Test avec curl pour voir l'erreur exacte...
curl -X POST "http://127.0.0.1:8000/api/login" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"email\":\"franco@gmail.com\",\"password\":\"I love teko.\"}" ^
  -v

echo.
echo.
pause
