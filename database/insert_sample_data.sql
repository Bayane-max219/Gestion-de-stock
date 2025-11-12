-- Données d'exemple pour SmartERP Pro
-- Boutiques et produits typiques de Madagascar

-- Insérer des utilisateurs/entreprises de démonstration
INSERT INTO users (id, email, password_hash, first_name, last_name, business_name, business_type, phone, city) VALUES
('550e8400-e29b-41d4-a716-446655440001', 'admin@demo.com', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rakoto', 'Jean', 'Épicerie Rakoto', 'epicerie', '034 12 345 67', 'Antananarivo'),
('550e8400-e29b-41d4-a716-446655440002', 'rabe@boutique.mg', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rabe', 'Marie', 'Superette Rabe', 'superette', '033 98 765 43', 'Fianarantsoa'),
('550e8400-e29b-41d4-a716-446655440003', 'andry@quinca.mg', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Andry', 'Paul', 'Quincaillerie Andry', 'quincaillerie', '032 11 222 33', 'Toamasina');

-- Insérer des catégories pour l'utilisateur demo
INSERT INTO categories (id, user_id, name, description) VALUES
('650e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440001', 'Alimentaire', 'Produits alimentaires de base'),
('650e8400-e29b-41d4-a716-446655440002', '550e8400-e29b-41d4-a716-446655440001', 'Hygiène', 'Produits d''hygiène et cosmétiques'),
('650e8400-e29b-41d4-a716-446655440003', '550e8400-e29b-41d4-a716-446655440001', 'Ménager', 'Produits d''entretien ménager'),
('650e8400-e29b-41d4-a716-446655440004', '550e8400-e29b-41d4-a716-446655440001', 'Boissons', 'Boissons et rafraîchissements');

-- Insérer des produits typiques malgaches pour l'utilisateur demo
INSERT INTO products (id, user_id, category_id, name, description, barcode, buy_price, sell_price, stock_quantity, min_stock) VALUES
-- Alimentaire
('750e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440001', 'Riz Makalioka 25kg', 'Riz blanc de qualité supérieure', '123456789', 38000, 45000, 50, 10),
('750e8400-e29b-41d4-a716-446655440002', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440001', 'Huile Lesieur 1L', 'Huile de tournesol pure', '987654321', 7000, 8500, 30, 15),
('750e8400-e29b-41d4-a716-446655440003', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440001', 'Sucre Sirama 1kg', 'Sucre blanc cristallisé', '654987321', 3200, 3800, 40, 20),
('750e8400-e29b-41d4-a716-446655440004', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440001', 'Pâtes Teza 500g', 'Pâtes alimentaires de qualité', '789123456', 2500, 3200, 75, 25),
('750e8400-e29b-41d4-a716-446655440005', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440001', 'Lait Concentré Nestlé', 'Lait concentré sucré', '321654987', 3800, 4500, 25, 10),
('750e8400-e29b-41d4-a716-446655440006', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440001', 'Farine Fleur 1kg', 'Farine de blé type 55', '147258369', 2800, 3500, 60, 15),

-- Hygiène
('750e8400-e29b-41d4-a716-446655440007', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440002', 'Savon Lux', 'Savon de toilette parfumé', '456789123', 2000, 2500, 100, 20),
('750e8400-e29b-41d4-a716-446655440008', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440002', 'Dentifrice Signal', 'Dentifrice protection complète', '963852741', 4500, 5500, 45, 15),
('750e8400-e29b-41d4-a716-446655440009', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440002', 'Shampoing Head & Shoulders', 'Shampoing antipelliculaire', '852741963', 8500, 10500, 30, 10),

-- Ménager
('750e8400-e29b-41d4-a716-446655440010', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440003', 'Lessive OMO 1kg', 'Lessive en poudre', '741963852', 6500, 8000, 35, 12),
('750e8400-e29b-41d4-a716-446655440011', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440003', 'Éponges Scotch-Brite', 'Pack de 5 éponges', '159753486', 1500, 2200, 80, 20),

-- Boissons
('750e8400-e29b-41d4-a716-446655440012', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440004', 'Coca-Cola 1.5L', 'Boisson gazeuse', '486159753', 2800, 3500, 48, 12),
('750e8400-e29b-41d4-a716-446655440013', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440004', 'Eau Cristalline 1.5L', 'Eau minérale naturelle', '357486159', 1200, 1500, 60, 20),
('750e8400-e29b-41d4-a716-446655440014', '550e8400-e29b-41d4-a716-446655440001', '650e8400-e29b-41d4-a716-446655440004', 'Thé Lipton 50 sachets', 'Thé noir en sachets', '963852741', 5200, 6500, 35, 10);

-- Insérer des clients pour l'utilisateur demo
INSERT INTO customers (id, user_id, name, phone, address, credit_limit, current_debt) VALUES
('850e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440001', 'Rakoto Jean', '034 11 111 11', 'Analakely, Antananarivo', 50000, 15500),
('850e8400-e29b-41d4-a716-446655440002', '550e8400-e29b-41d4-a716-446655440001', 'Rabe Marie', '033 22 222 22', 'Isotry, Antananarivo', 30000, 8200),
('850e8400-e29b-41d4-a716-446655440003', '550e8400-e29b-41d4-a716-446655440001', 'Andry Paul', '032 33 333 33', 'Behoririka, Antananarivo', 75000, 22800),
('850e8400-e29b-41d4-a716-446655440004', '550e8400-e29b-41d4-a716-446655440001', 'Hery Rasolofo', '034 44 444 44', 'Ankatso, Antananarivo', 40000, 0),
('850e8400-e29b-41d4-a716-446655440005', '550e8400-e29b-41d4-a716-446655440001', 'Nivo Randriamampionona', '033 55 555 55', 'Tsaralalàna, Antananarivo', 60000, 12300);

-- Insérer des ventes récentes pour l'utilisateur demo
INSERT INTO sales (id, user_id, customer_id, invoice_number, total_amount, payment_method, payment_status, amount_paid, created_at) VALUES
('950e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440001', '850e8400-e29b-41d4-a716-446655440001', 'FAC-001', 15500, 'credit', 'pending', 0, CURRENT_TIMESTAMP - INTERVAL '2 hours'),
('950e8400-e29b-41d4-a716-446655440002', '550e8400-e29b-41d4-a716-446655440001', '850e8400-e29b-41d4-a716-446655440002', 'FAC-002', 8200, 'credit', 'pending', 0, CURRENT_TIMESTAMP - INTERVAL '3 hours'),
('950e8400-e29b-41d4-a716-446655440003', '550e8400-e29b-41d4-a716-446655440001', '850e8400-e29b-41d4-a716-446655440003', 'FAC-003', 22800, 'credit', 'pending', 0, CURRENT_TIMESTAMP - INTERVAL '4 hours'),
('950e8400-e29b-41d4-a716-446655440004', '550e8400-e29b-41d4-a716-446655440001', NULL, 'FAC-004', 12500, 'cash', 'paid', 12500, CURRENT_TIMESTAMP - INTERVAL '5 hours'),
('950e8400-e29b-41d4-a716-446655440005', '550e8400-e29b-41d4-a716-446655440001', '850e8400-e29b-41d4-a716-446655440004', 'FAC-005', 35600, 'cash', 'paid', 35600, CURRENT_TIMESTAMP - INTERVAL '1 day');

-- Insérer des détails de vente
INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, total_price) VALUES
-- Vente FAC-001 (Rakoto Jean)
('950e8400-e29b-41d4-a716-446655440001', '750e8400-e29b-41d4-a716-446655440002', 1, 8500, 8500), -- Huile
('950e8400-e29b-41d4-a716-446655440001', '750e8400-e29b-41d4-a716-446655440007', 3, 2500, 7500), -- Savon

-- Vente FAC-002 (Rabe Marie)
('950e8400-e29b-41d4-a716-446655440002', '750e8400-e29b-41d4-a716-446655440004', 2, 3200, 6400), -- Pâtes
('950e8400-e29b-41d4-a716-446655440002', '750e8400-e29b-41d4-a716-446655440013', 1, 1500, 1500), -- Eau
('950e8400-e29b-41d4-a716-446655440002', '750e8400-e29b-41d4-a716-446655440008', 1, 5500, 5500), -- Dentifrice

-- Vente FAC-003 (Andry Paul)
('950e8400-e29b-41d4-a716-446655440003', '750e8400-e29b-41d4-a716-446655440001', 0.5, 45000, 22500), -- Riz (demi-sac)

-- Vente FAC-004 (Client comptant)
('950e8400-e29b-41d4-a716-446655440004', '750e8400-e29b-41d4-a716-446655440012', 3, 3500, 10500), -- Coca
('950e8400-e29b-41d4-a716-446655440004', '750e8400-e29b-41d4-a716-446655440014', 1, 6500, 6500), -- Thé

-- Vente FAC-005 (Hery Rasolofo)
('950e8400-e29b-41d4-a716-446655440005', '750e8400-e29b-41d4-a716-446655440001', 0.8, 45000, 36000); -- Riz

-- Insérer des mouvements de stock correspondants
INSERT INTO stock_movements (user_id, product_id, movement_type, quantity, reason, reference_id) VALUES
-- Mouvements pour les ventes
('550e8400-e29b-41d4-a716-446655440001', '750e8400-e29b-41d4-a716-446655440002', 'out', -1, 'Vente FAC-001', '950e8400-e29b-41d4-a716-446655440001'),
('550e8400-e29b-41d4-a716-446655440001', '750e8400-e29b-41d4-a716-446655440007', 'out', -3, 'Vente FAC-001', '950e8400-e29b-41d4-a716-446655440001'),
('550e8400-e29b-41d4-a716-446655440001', '750e8400-e29b-41d4-a716-446655440004', 'out', -2, 'Vente FAC-002', '950e8400-e29b-41d4-a716-446655440002'),
('550e8400-e29b-41d4-a716-446655440001', '750e8400-e29b-41d4-a716-446655440013', 'out', -1, 'Vente FAC-002', '950e8400-e29b-41d4-a716-446655440002'),
('550e8400-e29b-41d4-a716-446655440001', '750e8400-e29b-41d4-a716-446655440008', 'out', -1, 'Vente FAC-002', '950e8400-e29b-41d4-a716-446655440002');

-- Insérer des fournisseurs
INSERT INTO suppliers (id, user_id, name, contact_person, phone, email, address) VALUES
('a50e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440001', 'SIRAMA', 'Rakoto Distributeur', '020 22 123 45', 'contact@sirama.mg', 'Antsirabe, Madagascar'),
('a50e8400-e29b-41d4-a716-446655440002', '550e8400-e29b-41d4-a716-446655440001', 'Tiko', 'Rabe Commercial', '020 22 678 90', 'vente@tiko.mg', 'Antananarivo, Madagascar'),
('a50e8400-e29b-41d4-a716-446655440003', '550e8400-e29b-41d4-a716-446655440001', 'Unilever Madagascar', 'Andry Responsable', '020 22 345 67', 'madagascar@unilever.com', 'Antananarivo, Madagascar');
