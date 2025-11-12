<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllerUltraSimple;

/*
|--------------------------------------------------------------------------
| API Routes Ultra Simple - SmartERP Pro
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::get('/health', function() {
    return response()->json([
        'status' => 'OK', 
        'message' => 'SmartERP Pro Laravel API Ultra Simple',
        'version' => '1.0'
    ]);
});

Route::post('/login', [AuthControllerUltraSimple::class, 'login']);
Route::post('/register', [AuthControllerUltraSimple::class, 'register']);

// Routes protégées
Route::get('/me', [AuthControllerUltraSimple::class, 'me']);
Route::post('/logout', [AuthControllerUltraSimple::class, 'logout']);

// Dashboard simple
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

// Produits simple
Route::get('/products', function() {
    return response()->json([
        'success' => true,
        'data' => [
            ['id' => 1, 'name' => 'Produit Test', 'price' => 1000],
            ['id' => 2, 'name' => 'Autre Produit', 'price' => 2000]
        ]
    ]);
});

Route::post('/products', function() {
    return response()->json([
        'success' => true,
        'message' => 'Produit créé avec succès'
    ]);
});

// Ventes simple
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
        'message' => 'Vente créée avec succès'
    ]);
});
