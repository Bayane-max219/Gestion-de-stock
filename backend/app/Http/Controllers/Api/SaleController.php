<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    /**
     * Liste des ventes de l'utilisateur
     */
    public function index(Request $request)
    {
        $sales = Sale::byUser($request->user()->id)
                    ->with(['items.product'])
                    ->orderBy('sale_date', 'desc')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $sales->map(function($sale) {
                return [
                    'id' => $sale->id,
                    'customerName' => $sale->customer_name,
                    'total' => $sale->total,
                    'paymentMethod' => $sale->payment_method,
                    'timestamp' => $sale->sale_date->toISOString(),
                    'items' => $sale->items->map(function($item) {
                        return [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * Créer une vente - Basé sur la logique frontend processPayment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerName' => 'required|string|max:255',
            'paymentMethod' => 'required|in:cash,credit,card',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Calculer le total
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['quantity'] * $item['price'];
            }

            // Créer la vente
            $sale = Sale::create([
                'user_id' => $request->user()->id,
                'customer_name' => $request->customerName,
                'total' => $total,
                'payment_method' => $request->paymentMethod,
                'sale_date' => now()
            ]);

            // Créer les items et mettre à jour le stock
            foreach ($request->items as $itemData) {
                // Vérifier le stock disponible
                $product = Product::byUser($request->user()->id)->find($itemData['id']);
                
                if (!$product) {
                    throw new \Exception("Produit non trouvé: {$itemData['id']}");
                }

                if ($product->stock < $itemData['quantity']) {
                    throw new \Exception("Stock insuffisant pour {$product->name}. Stock disponible: {$product->stock}");
                }

                // Créer l'item de vente
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $itemData['id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price']
                ]);

                // Mettre à jour le stock (logique frontend)
                $product->decrement('stock', $itemData['quantity']);
            }

            DB::commit();

            // Recharger la vente avec les relations
            $sale->load(['items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Vente créée avec succès',
                'data' => [
                    'id' => $sale->id,
                    'customerName' => $sale->customer_name,
                    'total' => $sale->total,
                    'paymentMethod' => $sale->payment_method,
                    'timestamp' => $sale->sale_date->toISOString(),
                    'items' => $sale->items->map(function($item) {
                        return [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price
                        ];
                    })
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la vente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ventes du jour - Basé sur la logique frontend
     */
    public function today(Request $request)
    {
        $sales = Sale::byUser($request->user()->id)
                    ->today()
                    ->with(['items.product'])
                    ->get();

        $totalRevenue = $sales->sum('total');
        $totalTransactions = $sales->count();

        return response()->json([
            'success' => true,
            'data' => [
                'sales' => $sales->map(function($sale) {
                    return [
                        'id' => $sale->id,
                        'customerName' => $sale->customer_name,
                        'total' => $sale->total,
                        'timestamp' => $sale->sale_date->toISOString()
                    ];
                }),
                'summary' => [
                    'totalRevenue' => $totalRevenue,
                    'totalTransactions' => $totalTransactions,
                    'averageTicket' => $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0
                ]
            ]
        ]);
    }

    /**
     * Statistiques des ventes - Pour le dashboard
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        
        // Ventes d'aujourd'hui
        $todaySales = Sale::byUser($user->id)->today()->get();
        $todayRevenue = $todaySales->sum('total');
        $todayTransactions = $todaySales->count();

        // Ventes de cette semaine
        $weekSales = Sale::byUser($user->id)->thisWeek()->get();
        $weekRevenue = $weekSales->sum('total');

        // Ventes de ce mois
        $monthSales = Sale::byUser($user->id)->thisMonth()->get();
        $monthRevenue = $monthSales->sum('total');

        // Clients uniques
        $uniqueCustomers = Sale::byUser($user->id)
                              ->distinct('customer_name')
                              ->count('customer_name');

        return response()->json([
            'success' => true,
            'data' => [
                'today' => [
                    'revenue' => $todayRevenue,
                    'transactions' => $todayTransactions,
                    'averageTicket' => $todayTransactions > 0 ? $todayRevenue / $todayTransactions : 0
                ],
                'week' => [
                    'revenue' => $weekRevenue,
                    'transactions' => $weekSales->count()
                ],
                'month' => [
                    'revenue' => $monthRevenue,
                    'transactions' => $monthSales->count()
                ],
                'uniqueCustomers' => $uniqueCustomers
            ]
        ]);
    }
}
