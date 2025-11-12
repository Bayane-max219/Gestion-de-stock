<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuration MySQL
$host = '127.0.0.1';
$dbname = 'stock_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api_simple.php', '', $path);

// Route: POST /register
if ($method === 'POST' && $path === '/register') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        exit();
    }
    
    // Validation simple
    $required = ['firstName', 'lastName', 'email', 'password', 'businessName', 'businessType'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field $field is required"]);
            exit();
        }
    }
    
    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Email already exists']);
            exit();
        }
        
        // Insérer l'utilisateur
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password, business_name, business_type, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
        
        $stmt->execute([
            $input['firstName'],
            $input['lastName'],
            $input['email'],
            $hashedPassword,
            $input['businessName'],
            $input['businessType']
        ]);
        
        $userId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'id' => $userId,
                'firstName' => $input['firstName'],
                'lastName' => $input['lastName'],
                'email' => $input['email'],
                'businessName' => $input['businessName'],
                'businessType' => $input['businessType']
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
    }
    exit();
}

// Route: POST /login
if ($method === 'POST' && $path === '/login') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['email']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($input['password'], $user['password'])) {
            unset($user['password']); // Ne pas retourner le mot de passe
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'data' => $user,
                'token' => 'fake-token-' . $user['id'] // Token simple pour test
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Login failed: ' . $e->getMessage()]);
    }
    exit();
}

// Route: GET /products
if ($method === 'GET' && $path === '/products') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $products
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load products: ' . $e->getMessage()]);
    }
    exit();
}

// Route: POST /products
if ($method === 'POST' && $path === '/products') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required = ['name', 'category', 'buy_price', 'sell_price', 'stock'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field $field is required"]);
            exit();
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO products (name, category, buy_price, sell_price, stock, barcode, photo, user_id, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            $input['name'],
            $input['category'],
            $input['buy_price'],
            $input['sell_price'],
            $input['stock'],
            $input['barcode'] ?? null,
            $input['photo'] ?? null,
            1 // User ID par défaut
        ]);
        
        $productId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => ['id' => $productId] + $input
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create product: ' . $e->getMessage()]);
    }
    exit();
}

// Route: GET /sales
if ($method === 'GET' && $path === '/sales') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM sales ORDER BY sale_date DESC");
        $stmt->execute();
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $sales
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to load sales: ' . $e->getMessage()]);
    }
    exit();
}

// Route: POST /sales
if ($method === 'POST' && $path === '/sales') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required = ['customer_name', 'total', 'payment_method', 'items'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field $field is required"]);
            exit();
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Créer la vente
        $stmt = $pdo->prepare("
            INSERT INTO sales (customer_name, total, payment_method, sale_date, user_id, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            $input['customer_name'],
            $input['total'],
            $input['payment_method'],
            1 // User ID par défaut
        ]);
        
        $saleId = $pdo->lastInsertId();
        
        // Créer les items de vente
        foreach ($input['items'] as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO sale_items (sale_id, product_id, quantity, price, created_at, updated_at) 
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([
                $saleId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Sale created successfully',
            'data' => ['id' => $saleId] + $input
        ]);
        
    } catch (PDOException $e) {
        $pdo->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create sale: ' . $e->getMessage()]);
    }
    exit();
}

// Route par défaut
echo json_encode([
    'message' => 'SmartERP Pro Simple API',
    'status' => 'OK',
    'available_routes' => [
        'POST /register' => 'User registration',
        'POST /login' => 'User login',
        'GET /products' => 'Get all products',
        'POST /products' => 'Create product',
        'GET /sales' => 'Get all sales',
        'POST /sales' => 'Create sale'
    ]
]);
?>
