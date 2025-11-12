-- Données spécifiques pour QUINCAILLERIE
-- Produits typiques vendus dans une quincaillerie malgache

-- Créer un utilisateur quincaillerie pour la simulation
INSERT INTO users (id, email, password_hash, first_name, last_name, business_name, business_type, phone, city) VALUES
('550e8400-e29b-41d4-a716-446655440010', 'kevine@quincaillerie.mg', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kevine', 'Princy', 'Quincaillerie Kev', 'quincaillerie', '034 56 789 12', 'Antananarivo');

-- Créer des catégories pour quincaillerie
INSERT INTO categories (id, user_id, name, description) VALUES
('650e8400-e29b-41d4-a716-446655440010', '550e8400-e29b-41d4-a716-446655440010', 'Outils', 'Outils de bricolage et construction'),
('650e8400-e29b-41d4-a716-446655440011', '550e8400-e29b-41d4-a716-446655440010', 'Matériaux', 'Matériaux de construction'),
('650e8400-e29b-41d4-a716-446655440012', '550e8400-e29b-41d4-a716-446655440010', 'Électricité', 'Matériel électrique'),
('650e8400-e29b-41d4-a716-446655440013', '550e8400-e29b-41d4-a716-446655440010', 'Plomberie', 'Matériel de plomberie'),
('650e8400-e29b-41d4-a716-446655440014', '550e8400-e29b-41d4-a716-446655440010', 'Peinture', 'Peintures et accessoires');

-- PRODUITS À AJOUTER VIA LE TERMINAL (5 produits)
-- Ces produits seront déjà en base pour la simulation
INSERT INTO products (id, user_id, category_id, name, description, barcode, buy_price, sell_price, stock_quantity, min_stock) VALUES

-- 1. Marteau (Outils)
('750e8400-e29b-41d4-a716-446655440100', '550e8400-e29b-41d4-a716-446655440010', '650e8400-e29b-41d4-a716-446655440010', 'Marteau 500g', 'Marteau à panne fendue, manche bois', '1001001001', 8000, 12000, 25, 5),

-- 2. Ciment (Matériaux)
('750e8400-e29b-41d4-a716-446655440101', '550e8400-e29b-41d4-a716-446655440010', '650e8400-e29b-41d4-a716-446655440011', 'Ciment Portland 50kg', 'Sac de ciment Portland qualité supérieure', '1001001002', 18000, 25000, 40, 10),

-- 3. Ampoule LED (Électricité)
('750e8400-e29b-41d4-a716-446655440102', '550e8400-e29b-41d4-a716-446655440010', '650e8400-e29b-41d4-a716-446655440012', 'Ampoule LED 12W', 'Ampoule LED économique blanc chaud', '1001001003', 3500, 5500, 60, 15),

-- 4. Tuyau PVC (Plomberie)
('750e8400-e29b-41d4-a716-446655440103', '550e8400-e29b-41d4-a716-446655440010', '650e8400-e29b-41d4-a716-446655440013', 'Tuyau PVC Ø32mm', 'Tuyau PVC évacuation 32mm - 4m', '1001001004', 4500, 7000, 30, 8),

-- 5. Peinture Murale (Peinture)
('750e8400-e29b-41d4-a716-446655440104', '550e8400-e29b-41d4-a716-446655440010', '650e8400-e29b-41d4-a716-446655440014', 'Peinture Murale 4L Blanc', 'Peinture acrylique mate pour intérieur', '1001001005', 15000, 22000, 20, 5);

-- Créer quelques clients pour la quincaillerie
INSERT INTO customers (id, user_id, name, phone, address, credit_limit, current_debt) VALUES
('850e8400-e29b-41d4-a716-446655440100', '550e8400-e29b-41d4-a716-446655440010', 'Rakoto Maçon', '034 11 222 33', 'Analakely, Antananarivo', 100000, 0),
('850e8400-e29b-41d4-a716-446655440101', '550e8400-e29b-41d4-a716-446655440010', 'Rabe Électricien', '033 44 555 66', 'Isotry, Antananarivo', 75000, 0),
('850e8400-e29b-41d4-a716-446655440102', '550e8400-e29b-41d4-a716-446655440010', 'Hery Plombier', '032 77 888 99', 'Behoririka, Antananarivo', 50000, 0);

-- Créer quelques fournisseurs pour la quincaillerie
INSERT INTO suppliers (id, user_id, name, contact_person, phone, email, address) VALUES
('a50e8400-e29b-41d4-a716-446655440100', '550e8400-e29b-41d4-a716-446655440010', 'Holcim Madagascar', 'Andry Commercial', '020 22 111 22', 'vente@holcim.mg', 'Antananarivo, Madagascar'),
('a50e8400-e29b-41d4-a716-446655440101', '550e8400-e29b-41d4-a716-446655440010', 'Électro-Mada', 'Rabe Responsable', '020 22 333 44', 'contact@electromada.mg', 'Antananarivo, Madagascar'),
('a50e8400-e29b-41d4-a716-446655440102', '550e8400-e29b-41d4-a716-446655440010', 'Outillage Pro', 'Hery Directeur', '020 22 555 66', 'info@outillagepro.mg', 'Antananarivo, Madagascar');
