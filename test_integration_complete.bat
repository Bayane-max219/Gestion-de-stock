@echo off
echo 🧪 TEST INTÉGRATION COMPLÈTE SMARTERP PRO
echo ========================================

echo 📍 Étape 1: Vérification MySQL...
echo.

REM Test connexion MySQL
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT COUNT(*) as tables_count FROM information_schema.tables WHERE table_schema = 'stock_management';" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ MySQL connecté - Base stock_management accessible
) else (
    echo ❌ Problème MySQL
    pause
    exit /b 1
)

echo.
echo 📍 Étape 2: Test API Laravel...
echo.

REM Test endpoint Laravel
curl -s -X GET "http://localhost:8000/api/dashboard" -H "Accept: application/json" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo ✅ API Laravel accessible sur http://localhost:8000
) else (
    echo ❌ API Laravel non accessible
    echo 💡 Démarrez le serveur Laravel: php artisan serve
    pause
    exit /b 1
)

echo.
echo 📍 Étape 3: Test inscription utilisateur...
echo.

REM Test inscription avec données réelles
curl -s -X POST "http://localhost:8000/api/register" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"firstName\":\"Miguel\",\"lastName\":\"Test\",\"email\":\"miguel.test@smarterp.com\",\"password\":\"password123\",\"confirmPassword\":\"password123\",\"businessName\":\"Boutique Test\",\"businessType\":\"epicerie\",\"phone\":\"0123456789\",\"acceptTerms\":true}" > temp_response.json

if exist temp_response.json (
    echo 📄 Réponse API:
    type temp_response.json
    echo.
    
    REM Vérifier si l'utilisateur est créé en base
    echo 📍 Vérification en base MySQL...
    "C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT id, first_name, last_name, email, business_name FROM users WHERE email = 'miguel.test@smarterp.com';"
    
    del temp_response.json
) else (
    echo ❌ Pas de réponse de l'API
)

echo.
echo 📍 Étape 4: Vérification Frontend...
echo.

REM Test si le frontend est accessible
curl -s -I "http://localhost:5173" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo ✅ Frontend Vue.js accessible sur http://localhost:5173
) else (
    echo ❌ Frontend non accessible
    echo 💡 Démarrez le frontend: npm run dev
)

echo.
echo 🎯 RÉSUMÉ:
echo - MySQL: Base stock_management avec tables
echo - Laravel: API sur http://localhost:8000
echo - Vue.js: Frontend sur http://localhost:5173
echo.
echo 💡 Si tout est ✅, l'intégration devrait fonctionner !
echo.

pause
