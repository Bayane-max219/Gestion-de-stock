@echo off
echo 🔍 VÉRIFICATION TABLES MYSQL
echo ============================

echo 📍 Tables existantes dans stock_management...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SHOW TABLES;"

echo.
echo 📍 Structure table users...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; DESCRIBE users;"

echo.
echo 📍 Vérification table products...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SHOW TABLES LIKE 'products';"

echo.
echo 📍 Vérification table sales...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SHOW TABLES LIKE 'sales';"

echo.
pause
