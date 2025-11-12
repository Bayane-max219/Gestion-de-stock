-- Requêtes d'exemple pour SmartERP Pro
-- Utilisez ces requêtes pour tester et explorer les données

-- 1. Voir tous les utilisateurs/entreprises
SELECT 
    business_name,
    first_name || ' ' || last_name as owner,
    business_type,
    city,
    email
FROM users
ORDER BY created_at;

-- 2. Voir tous les produits avec leurs catégories (pour l'utilisateur demo)
SELECT 
    p.name as produit,
    c.name as categorie,
    p.barcode,
    p.buy_price as prix_achat,
    p.sell_price as prix_vente,
    p.stock_quantity as stock,
    p.min_stock as stock_min,
    CASE 
        WHEN p.stock_quantity <= p.min_stock THEN 'ALERTE STOCK FAIBLE'
        ELSE 'OK'
    END as statut_stock
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.user_id = '550e8400-e29b-41d4-a716-446655440001'
ORDER BY c.name, p.name;

-- 3. Voir les ventes récentes avec détails
SELECT 
    s.invoice_number as facture,
    COALESCE(cu.name, 'Client comptant') as client,
    s.total_amount as montant,
    s.payment_method as paiement,
    s.payment_status as statut,
    s.created_at as date_vente,
    EXTRACT(EPOCH FROM (CURRENT_TIMESTAMP - s.created_at))/3600 as heures_depuis
FROM sales s
LEFT JOIN customers cu ON s.customer_id = cu.id
WHERE s.user_id = '550e8400-e29b-41d4-a716-446655440001'
ORDER BY s.created_at DESC
LIMIT 10;

-- 4. Détails d'une vente spécifique
SELECT 
    s.invoice_number as facture,
    p.name as produit,
    si.quantity as quantite,
    si.unit_price as prix_unitaire,
    si.total_price as total_ligne,
    s.total_amount as total_facture
FROM sales s
JOIN sale_items si ON s.id = si.sale_id
JOIN products p ON si.product_id = p.id
WHERE s.invoice_number = 'FAC-001'
ORDER BY si.id;

-- 5. Top 5 des produits les plus vendus
SELECT 
    p.name as produit,
    SUM(si.quantity) as quantite_vendue,
    SUM(si.total_price) as chiffre_affaires,
    COUNT(DISTINCT si.sale_id) as nombre_ventes
FROM sale_items si
JOIN products p ON si.product_id = p.id
JOIN sales s ON si.sale_id = s.id
WHERE s.user_id = '550e8400-e29b-41d4-a716-446655440001'
GROUP BY p.id, p.name
ORDER BY quantite_vendue DESC
LIMIT 5;

-- 6. Chiffre d'affaires par jour (7 derniers jours)
SELECT 
    DATE(created_at) as jour,
    COUNT(*) as nombre_ventes,
    SUM(total_amount) as chiffre_affaires,
    AVG(total_amount) as ticket_moyen
FROM sales
WHERE user_id = '550e8400-e29b-41d4-a716-446655440001'
    AND created_at >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY DATE(created_at)
ORDER BY jour DESC;

-- 7. Clients avec crédit en cours
SELECT 
    name as client,
    phone as telephone,
    credit_limit as limite_credit,
    current_debt as dette_actuelle,
    (credit_limit - current_debt) as credit_disponible,
    ROUND((current_debt / credit_limit * 100), 2) as pourcentage_utilise
FROM customers
WHERE user_id = '550e8400-e29b-41d4-a716-446655440001'
    AND current_debt > 0
ORDER BY current_debt DESC;

-- 8. Produits en rupture ou stock faible
SELECT 
    p.name as produit,
    c.name as categorie,
    p.stock_quantity as stock_actuel,
    p.min_stock as stock_minimum,
    (p.min_stock - p.stock_quantity) as manque,
    p.sell_price as prix_vente
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.user_id = '550e8400-e29b-41d4-a716-446655440001'
    AND p.stock_quantity <= p.min_stock
ORDER BY (p.min_stock - p.stock_quantity) DESC;

-- 9. Mouvements de stock récents
SELECT 
    p.name as produit,
    sm.movement_type as type_mouvement,
    sm.quantity as quantite,
    sm.reason as raison,
    sm.created_at as date_mouvement
FROM stock_movements sm
JOIN products p ON sm.product_id = p.id
WHERE sm.user_id = '550e8400-e29b-41d4-a716-446655440001'
ORDER BY sm.created_at DESC
LIMIT 20;

-- 10. Statistiques générales de l'entreprise
SELECT 
    'Produits' as type,
    COUNT(*) as nombre
FROM products 
WHERE user_id = '550e8400-e29b-41d4-a716-446655440001'

UNION ALL

SELECT 
    'Clients' as type,
    COUNT(*) as nombre
FROM customers 
WHERE user_id = '550e8400-e29b-41d4-a716-446655440001'

UNION ALL

SELECT 
    'Ventes (ce mois)' as type,
    COUNT(*) as nombre
FROM sales 
WHERE user_id = '550e8400-e29b-41d4-a716-446655440001'
    AND created_at >= DATE_TRUNC('month', CURRENT_DATE)

UNION ALL

SELECT 
    'CA ce mois (Ar)' as type,
    COALESCE(SUM(total_amount), 0) as nombre
FROM sales 
WHERE user_id = '550e8400-e29b-41d4-a716-446655440001'
    AND created_at >= DATE_TRUNC('month', CURRENT_DATE);
