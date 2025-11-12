@echo off
echo 🔧 CRÉATION TABLES MANQUANTES
echo ==============================

echo 📍 Création table products si manquante...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT DEFAULT 1, name VARCHAR(255) NOT NULL, description TEXT, price DECIMAL(10,2) DEFAULT 0, stock_quantity INT DEFAULT 0, category VARCHAR(100), barcode VARCHAR(50), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);"

echo 📍 Création table sales si manquante...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; CREATE TABLE IF NOT EXISTS sales (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT DEFAULT 1, customer_name VARCHAR(255), customer_phone VARCHAR(50), total DECIMAL(10,2) DEFAULT 0, payment_method VARCHAR(50) DEFAULT 'cash', sale_date DATE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);"

echo 📍 Création table sale_items si manquante...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; CREATE TABLE IF NOT EXISTS sale_items (id INT AUTO_INCREMENT PRIMARY KEY, sale_id INT, product_id INT, quantity INT DEFAULT 1, price DECIMAL(10,2) DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE);"

echo ✅ Tables créées !

echo 📍 Vérification finale...
"C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe" -u root -e "USE stock_management; SHOW TABLES;"

pause
