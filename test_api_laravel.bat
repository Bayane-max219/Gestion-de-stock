@echo off
echo 🧪 TEST API LARAVEL SMARTERP PRO
echo ==============================

echo 📍 Test de connexion à l'API Laravel...
echo.

REM Test de base - endpoint de test
curl -X GET "http://localhost:8000/api/dashboard" -H "Accept: application/json" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ API Laravel accessible
) else (
    echo ❌ API Laravel non accessible
    echo 💡 Vérifiez que le serveur Laravel est démarré
)

echo.
echo 🔍 Test d'inscription utilisateur...
curl -X POST "http://localhost:8000/api/register" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"firstName\":\"Test\",\"lastName\":\"User\",\"email\":\"test@example.com\",\"password\":\"password123\",\"confirmPassword\":\"password123\",\"businessName\":\"Test Business\",\"businessType\":\"epicerie\",\"acceptTerms\":true}" 2>nul

echo.
echo 💡 Si vous voyez des données JSON, l'API fonctionne !
echo 💡 Sinon, vérifiez les logs Laravel
echo.

pause
