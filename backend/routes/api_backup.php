<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllerSimple;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;

/*
|--------------------------------------------------------------------------
| API Routes Simplifiées - SmartERP Pro
|--------------------------------------------------------------------------
| Routes basées sur la logique frontend Vue.js
*/

// Middleware CORS pour toutes les routes API
Route::middleware(['cors'])->group(function () {

// Routes publiques
Route::get('/health', function() {
    return response()->json(['status' => 'OK', 'message' => 'Laravel API is running']);
});
Route::post('/login', [AuthControllerSimple::class, 'login']);
Route::post('/register', [AuthControllerSimple::class, 'register']);

// Routes protégées (sans Sanctum pour test)
Route::group([], function () {
    
    // Authentification
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Produits - Basé sur StockPage.vue
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/products/search/barcode/{barcode}', [ProductController::class, 'searchByBarcode']);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock']);

    // Ventes - Basé sur SalesPage.vue
    Route::get('/sales', [SaleController::class, 'index']);
    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/sales/today', [SaleController::class, 'today']);
    Route::get('/sales/stats', [SaleController::class, 'stats']);

    // Dashboard - Données pour DashboardPage.vue
    Route::get('/dashboard', function(\Illuminate\Http\Request $request) {
        $user = $request->user();
        
        // Statistiques basées sur la logique frontend
        $todaySales = \App\Models\Sale::where('user_id', $user->id)
                                    ->whereDate('sale_date', today())
                                    ->get();
        $totalProducts = \App\Models\Product::where('user_id', $user->id)->count();
        $uniqueCustomers = \App\Models\Sale::where('user_id', $user->id)
                                          ->distinct('customer_name')
                                          ->count('customer_name');
        
        return response()->json([
            'success' => true,
            'data' => [
                'todayRevenue' => $todaySales->sum('total'),
                'todayTransactions' => $todaySales->count(),
                'totalProducts' => $totalProducts,
                'totalClients' => $uniqueCustomers
            ]
        ]);
    });

    // Reports - Pour ReportsPage.vue
    Route::get('/reports/top-products', function(\Illuminate\Http\Request $request) {
        $user = $request->user();
        
        $topProducts = \App\Models\SaleItem::whereHas('sale', function($query) use ($user) {
                                              $query->where('user_id', $user->id);
                                          })
                                          ->with('product')
                                          ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(quantity * price) as total_revenue')
                                          ->groupBy('product_id')
                                          ->orderBy('total_revenue', 'desc')
                                          ->limit(5)
                                          ->get();
        
        return response()->json([
            'success' => true,
            'data' => $topProducts->map(function($item) {
                return [
                    'name' => $item->product->name,
                    'totalSold' => $item->total_sold,
                    'totalRevenue' => $item->total_revenue
                ];
            })
        ]);
    });

}); // Fin du Route::group

}); // Fin du middleware CORS