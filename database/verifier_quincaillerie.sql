-- Vérification des données Quincaillerie Kev
-- Exécuter après avoir inséré les données

\echo '========================================='
\echo '    QUINCAILLERIE KEV - VÉRIFICATION'
\echo '========================================='

\echo ''
\echo '1. UTILISATEUR CRÉÉ :'
SELECT 
    business_name as "Entreprise",
    first_name || ' ' || last_name as "Propriétaire",
    email as "Email",
    city as "Ville"
FROM users 
WHERE email = 'kevine@quincaillerie.mg';

\echo ''
\echo '2. CATÉGORIES CRÉÉES :'
SELECT 
    name as "Catégorie",
    description as "Description"
FROM categories 
WHERE user_id = '550e8400-e29b-41d4-a716-446655440010'
ORDER BY name;

\echo ''
\echo '3. PRODUITS DÉJÀ EN BASE (via terminal) :'
SELECT 
    p.name as "Produit",
    c.name as "Catégorie",
    p.barcode as "Code-barres",
    p.sell_price as "Prix (Ar)",
    p.stock_quantity as "Stock"
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.user_id = '550e8400-e29b-41d4-a716-446655440010'
ORDER BY c.name, p.name;

\echo ''
\echo '4. CLIENTS CRÉÉS :'
SELECT 
    name as "Client",
    phone as "Téléphone",
    credit_limit as "Limite crédit (Ar)"
FROM customers 
WHERE user_id = '550e8400-e29b-41d4-a716-446655440010';

\echo ''
\echo '5. FOURNISSEURS CRÉÉS :'
SELECT 
    name as "Fournisseur",
    contact_person as "Contact",
    phone as "Téléphone"
FROM suppliers 
WHERE user_id = '550e8400-e29b-41d4-a716-446655440010';

\echo ''
\echo '6. RÉSUMÉ STOCK :'
SELECT 
    COUNT(*) as "Nombre produits",
    SUM(stock_quantity) as "Total unités",
    SUM(stock_quantity * sell_price) as "Valeur stock (Ar)"
FROM products 
WHERE user_id = '550e8400-e29b-41d4-a716-446655440010';

\echo ''
\echo '========================================='
\echo 'PRÊT POUR LA SIMULATION !'
\echo ''
\echo 'Connexion :'
\echo '- Email: kevine@quincaillerie.mg'
\echo '- Mot de passe: password123'
\echo ''
\echo 'À faire maintenant :'
\echo '1. Se connecter avec ce compte'
\echo '2. Aller dans Gestion Stock'
\echo '3. Ajouter 5 produits via interface'
\echo '4. Faire des ventes de test'
\echo '========================================='
