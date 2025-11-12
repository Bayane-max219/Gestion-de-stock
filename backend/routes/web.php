<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllerMySQL;

/*
|--------------------------------------------------------------------------
| Web Routes + API Routes (forcé)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'SmartERP Pro API',
        'status' => 'OK',
        'version' => '1.0'
    ]);
});

// Routes API forcées dans web.php
Route::prefix('api')->group(function () {
    
    // Health check
    Route::get('/health', function() {
        return response()->json([
            'status' => 'OK', 
            'message' => 'SmartERP Pro Laravel API',
            'version' => '1.0'
        ]);
    });

    // Auth routes avec MySQL
    Route::post('/login', [AuthControllerMySQL::class, 'login']);
    Route::post('/register', [AuthControllerMySQL::class, 'register']);
    Route::get('/me', [AuthControllerMySQL::class, 'me']);
    Route::post('/logout', [AuthControllerMySQL::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', function() {
        return response()->json([
            'success' => true,
            'data' => [
                'todayRevenue' => 150000,
                'todayTransactions' => 25,
                'totalProducts' => 120,
                'totalClients' => 45
            ]
        ]);
    });

    // Produits
    Route::get('/products', function() {
        return response()->json([
            'success' => true,
            'data' => [
                ['id' => 1, 'name' => 'Produit Test 1', 'price' => 1000],
                ['id' => 2, 'name' => 'Produit Test 2', 'price' => 2000]
            ]
        ]);
    });

    Route::post('/products', function() {
        return response()->json([
            'success' => true,
            'message' => 'Produit créé'
        ]);
    });

    // Ventes
    Route::get('/sales', function() {
        return response()->json([
            'success' => true,
            'data' => [
                ['id' => 1, 'customer' => 'Client Test', 'total' => 5000],
                ['id' => 2, 'customer' => 'Autre Client', 'total' => 3000]
            ]
        ]);
    });

    Route::post('/sales', function() {
        return response()->json([
            'success' => true,
            'message' => 'Vente créée'
        ]);
    });
    
});
