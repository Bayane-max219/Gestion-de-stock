@echo off
echo 🔍 TEST COMPTES MYSQL RÉELS
echo ===========================

echo 📍 Test health API...
curl -s "http://127.0.0.1:8000/api/health"

echo.
echo.
echo 📍 Test login Franco (franco@gmail.com)...
curl -s -X POST "http://127.0.0.1:8000/api/login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"franco@gmail.com\",\"password\":\"I love teko.\"}"

echo.
echo.
echo 📍 Test login Fatima (fatima@gmail.com)...
curl -s -X POST "http://127.0.0.1:8000/api/login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"fatima@gmail.com\",\"password\":\"quincaillerie\"}"

echo.
echo.
echo 📍 Test login Miguel (miguel.final@smarterp.com)...
curl -s -X POST "http://127.0.0.1:8000/api/login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"miguel.final@smarterp.com\",\"password\":\"password\"}"

echo.
echo.
echo 🎯 Si un des logins fonctionne:
echo 1. Allez sur http://localhost:5174
echo 2. Utilisez les mêmes identifiants
echo 3. L'application devrait se connecter avec MySQL !
echo.

pause
