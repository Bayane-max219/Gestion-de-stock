<?php
// Index minimal pour tester Laravel
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
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
