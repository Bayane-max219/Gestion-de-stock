-- Création de la base de données SmartERP Pro
DROP DATABASE IF EXISTS stock_management;
CREATE DATABASE stock_management
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'French_France.1252'
    LC_CTYPE = 'French_France.1252'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

-- Se connecter à la base
\c stock_management;

-- Créer l'extension UUID si nécessaire
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Message de confirmation
SELECT 'Base de données stock_management créée avec succès!' as message;
