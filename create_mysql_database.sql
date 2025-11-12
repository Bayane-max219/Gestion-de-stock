-- Création de la base de données SmartERP Pro avec MySQL
DROP DATABASE IF EXISTS stock_management;
CREATE DATABASE stock_management 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Utiliser la base
USE stock_management;

-- Message de confirmation
SELECT 'Base de données stock_management créée avec succès!' as message;
