<?php
echo "🧪 TEST CONNEXION POSTGRESQL\n";
echo "============================\n\n";

// Configuration
$host = '127.0.0.1';
$port = '5432';
$dbname = 'smarterp_pro';
$user = 'postgres';
$password = 'postgres';

try {
    // Test de connexion
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion PostgreSQL réussie !\n";
    echo "📊 Base de données: $dbname\n";
    echo "🏠 Serveur: $host:$port\n";
    echo "👤 Utilisateur: $user\n\n";
    
    // Test de création de table
    $sql = "CREATE TABLE IF NOT EXISTS test_connection (
        id SERIAL PRIMARY KEY,
        message VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✅ Création de table test réussie\n";
    
    // Insertion de test
    $stmt = $pdo->prepare("INSERT INTO test_connection (message) VALUES (?)");
    $stmt->execute(['Test connexion SmartERP Pro - ' . date('Y-m-d H:i:s')]);
    echo "✅ Insertion de données test réussie\n";
    
    // Lecture de test
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM test_connection");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Lecture de données test réussie - {$result['total']} enregistrements\n\n";
    
    echo "🎯 RÉSULTAT: PostgreSQL est prêt pour Laravel !\n";
    
} catch (PDOException $e) {
    echo "❌ ERREUR DE CONNEXION POSTGRESQL:\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    
    echo "🔧 SOLUTIONS POSSIBLES:\n";
    echo "1. Vérifier que PostgreSQL est démarré\n";
    echo "2. Vérifier que la base 'smarterp_pro' existe\n";
    echo "3. Vérifier les identifiants (postgres/postgres)\n";
    echo "4. Vérifier le port 5432\n\n";
    
    echo "💡 Commandes utiles:\n";
    echo "- Créer la base: CREATE DATABASE smarterp_pro;\n";
    echo "- Se connecter: psql -U postgres -h localhost\n";
}
?>
