<?php
// Index minimal pour tester Laravel
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers CORS (une seule fois)
header('Access-Control-Allow-Origin: http://localhost:5174');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Test simple sans Laravel
if ($path === '/api/health') {
    echo json_encode(['status' => 'OK', 'message' => 'Test direct PHP', 'time' => date('H:i:s')]);
    exit();
}

if ($method === 'POST' && $path === '/api/login') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Test connexion MySQL directe
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=stock_management', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$input['email'] ?? '']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($input['password'] ?? '', $user['password'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Login MySQL direct réussi',
                'data' => [
                    'id' => $user['id'],
                    'firstName' => $user['first_name'],
                    'lastName' => $user['last_name'],
                    'email' => $user['email'],
                    'businessName' => $user['business_name'],
                    'businessType' => $user['business_type']
                ],
                'token' => 'direct-mysql-' . $user['id']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Identifiants incorrects',
                'debug' => [
                    'user_found' => $user ? 'yes' : 'no',
                    'email_searched' => $input['email'] ?? 'none'
                ]
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur MySQL: ' . $e->getMessage()
        ]);
    }
    exit();
}

// Route: POST /api/register
if ($method === 'POST' && $path === '/api/register') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=stock_management', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Vérifier si email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$input['email'] ?? '']);
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Email déjà utilisé'
            ]);
            exit();
        }
        
        // Créer nouvel utilisateur
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, business_name, business_type, accept_terms, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");
        $stmt->execute([
            $input['firstName'] ?? '',
            $input['lastName'] ?? '',
            $input['email'] ?? '',
            password_hash($input['password'] ?? '', PASSWORD_DEFAULT),
            $input['businessName'] ?? '',
            $input['businessType'] ?? 'epicerie'
        ]);
        
        $userId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Inscription réussie',
            'data' => [
                'id' => $userId,
                'firstName' => $input['firstName'] ?? '',
                'lastName' => $input['lastName'] ?? '',
                'email' => $input['email'] ?? '',
                'businessName' => $input['businessName'] ?? '',
                'businessType' => $input['businessType'] ?? 'epicerie'
            ],
            'token' => 'mysql-token-' . $userId
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur inscription: ' . $e->getMessage()
        ]);
    }
    exit();
}

// Route: GET /api/dashboard
if ($method === 'GET' && $path === '/api/dashboard') {
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=stock_management', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Pour un nouvel utilisateur, tout devrait être à 0
        // Plus tard on pourra récupérer l'ID utilisateur du token
        
        // Compter les produits (pour test, on prend tous les produits)
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
        $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Compter les ventes d'aujourd'hui (pour test, on prend toutes les ventes)
        $stmt = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as revenue FROM sales WHERE DATE(created_at) = CURDATE()");
        $salesData = $stmt->fetch(PDO::FETCH_ASSOC);
        $todayTransactions = $salesData['total'] ?? 0;
        $todayRevenue = $salesData['revenue'] ?? 0;
        
        // Compter les clients uniques (pour test)
        $stmt = $pdo->query("SELECT COUNT(DISTINCT customer_name) as total FROM sales WHERE customer_name IS NOT NULL AND customer_name != ''");
        $totalClients = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        echo json_encode([
            'success' => true,
            'data' => [
                'todayRevenue' => (int)$todayRevenue,
                'todayTransactions' => (int)$todayTransactions,
                'totalProducts' => (int)$totalProducts,
                'totalClients' => (int)$totalClients
            ]
        ]);
        
    } catch (Exception $e) {
        // Si erreur MySQL, retourner des zéros pour nouvel utilisateur
        echo json_encode([
            'success' => true,
            'data' => [
                'todayRevenue' => 0,
                'todayTransactions' => 0,
                'totalProducts' => 0,
                'totalClients' => 0
            ]
        ]);
    }
    exit();
}

// Essayer de charger Laravel normalement
try {
    require_once __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    $response->send();
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Laravel failed to load',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
