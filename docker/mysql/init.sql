-- ─────────────────────────────────────────────────────────────────
-- SmartERP Pro — MySQL initialization script
-- Exécuté automatiquement au premier démarrage du container MySQL
-- ─────────────────────────────────────────────────────────────────

CREATE DATABASE IF NOT EXISTS `stock_management`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON `stock_management`.* TO 'smarterp'@'%';
FLUSH PRIVILEGES;
