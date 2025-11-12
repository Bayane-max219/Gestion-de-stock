@echo off
echo 🔍 TEST CONNEXION MYSQL
echo =======================

echo 📍 Test connexion MySQL directe...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT COUNT(*) as total_users FROM users;"

echo.
echo 📍 Affichage des comptes existants...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SELECT id, first_name, last_name, email, business_name FROM users;"

echo.
echo 📍 Test Laravel avec MySQL...
cd backend
C:\wamp64\bin\php\php8.2.0\php.exe -r "
try {
    require_once 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$pdo = new PDO('mysql:host=127.0.0.1;dbname=stock_management', 'root', '');
    \$stmt = \$pdo->query('SELECT email FROM users LIMIT 3');
    echo 'Connexion MySQL OK - Comptes trouvés:' . PHP_EOL;
    while (\$row = \$stmt->fetch()) {
        echo '- ' . \$row['email'] . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Erreur MySQL: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
pause
