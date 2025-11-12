-- 🐘 CRÉATION DE LA BASE DE DONNÉES SMARTERP PRO
-- Exécuter dans pgAdmin ou psql

-- Créer la base de données
DROP DATABASE IF EXISTS smarterp_pro;
CREATE DATABASE smarterp_pro
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'French_France.1252'
    LC_CTYPE = 'French_France.1252'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

-- Se connecter à la base
\c smarterp_pro;

-- Créer l'extension UUID (pour les clés primaires)
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

COMMENT ON DATABASE smarterp_pro IS 'Base de données SmartERP Pro - Gestion de stock Madagascar';
