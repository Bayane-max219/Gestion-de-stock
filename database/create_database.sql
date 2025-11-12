-- Création de la base de données SmartERP Pro
-- Exécuter en tant que superuser PostgreSQL

-- Créer la base de données
CREATE DATABASE smarterp_pro
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'French_France.1252'
    LC_CTYPE = 'French_France.1252'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

-- Se connecter à la base de données
\c smarterp_pro;

-- Créer l'extension pour les UUID
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

COMMENT ON DATABASE smarterp_pro IS 'Base de données pour SmartERP Pro - Système de gestion pour boutiques malgaches';
