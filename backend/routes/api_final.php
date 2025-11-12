<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllerClean;

/*
|--------------------------------------------------------------------------
| API Routes - SmartERP Pro Clean
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::get('/health', function() {
    return response()->json([
        'status' => 'OK', 
        'message' => 'SmartERP Pro Laravel API',
        'version' => '1.0'
    ]);
});

Route::post('/login', [AuthControllerClean::class, 'login']);
Route::post('/register', [AuthControllerClean::class, 'register']);

// Routes protégées
Route::get('/me', [AuthControllerClean::class, 'me']);
Route::post('/logout', [AuthControllerClean::class, 'logout']);

// Dashboard
Route::get('/dashboard', function() {
    return response()->json([
        'success' => true,
        'data' => [
            'todayRevenue' => 0,
            'todayTransactions' => 0,
            'totalProducts' => 0,
            'totalClients' => 0
        ]
    ]);
});

// Produits (placeholder)
Route::get('/products', function() {
    return response()->json([
        'success' => true,
        'data' => []
    ]);
});

Route::post('/products', function() {
    return response()->json([
        'success' => true,
        'message' => 'Produit créé'
    ]);
});

// Ventes (placeholder)
Route::get('/sales', function() {
    return response()->json([
        'success' => true,
        'data' => []
    ]);
});

Route::post('/sales', function() {
    return response()->json([
        'success' => true,
        'message' => 'Vente créée'
    ]);
});
