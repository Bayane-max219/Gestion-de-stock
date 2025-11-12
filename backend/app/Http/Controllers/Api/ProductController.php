<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Liste des produits de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $products = Product::byUser($request->user()->id)
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category,
                    'buyPrice' => $product->buy_price,
                    'sellPrice' => $product->sell_price,
                    'stock' => $product->stock,
                    'barcode' => $product->barcode,
                    'photo' => $product->photo,
                    'profit' => $product->getProfit(),
                    'isLowStock' => $product->isLowStock()
                ];
            })
        ]);
    }

    /**
     * Créer un produit - Basé sur la logique frontend newProduct
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'buyPrice' => 'required|numeric|min:0',
            'sellPrice' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:50',
            'photo' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::create([
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'buy_price' => $request->buyPrice,
                'sell_price' => $request->sellPrice,
                'stock' => $request->stock,
                'barcode' => $request->barcode,
                'photo' => $request->photo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produit créé avec succès',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category,
                    'buyPrice' => $product->buy_price,
                    'sellPrice' => $product->sell_price,
                    'stock' => $product->stock,
                    'barcode' => $product->barcode,
                    'photo' => $product->photo
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un produit
     */
    public function show(Request $request, $id)
    {
        $product = Product::byUser($request->user()->id)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'category' => $product->category,
                'buyPrice' => $product->buy_price,
                'sellPrice' => $product->sell_price,
                'stock' => $product->stock,
                'barcode' => $product->barcode,
                'photo' => $product->photo
            ]
        ]);
    }

    /**
     * Mettre à jour un produit
     */
    public function update(Request $request, $id)
    {
        $product = Product::byUser($request->user()->id)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'sometimes|string|max:100',
            'buyPrice' => 'sometimes|numeric|min:0',
            'sellPrice' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'barcode' => 'nullable|string|max:50',
            'photo' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [];
            if ($request->has('name')) $updateData['name'] = $request->name;
            if ($request->has('description')) $updateData['description'] = $request->description;
            if ($request->has('category')) $updateData['category'] = $request->category;
            if ($request->has('buyPrice')) $updateData['buy_price'] = $request->buyPrice;
            if ($request->has('sellPrice')) $updateData['sell_price'] = $request->sellPrice;
            if ($request->has('stock')) $updateData['stock'] = $request->stock;
            if ($request->has('barcode')) $updateData['barcode'] = $request->barcode;
            if ($request->has('photo')) $updateData['photo'] = $request->photo;

            $product->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Produit mis à jour avec succès',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category,
                    'buyPrice' => $product->buy_price,
                    'sellPrice' => $product->sell_price,
                    'stock' => $product->stock,
                    'barcode' => $product->barcode,
                    'photo' => $product->photo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un produit
     */
    public function destroy(Request $request, $id)
    {
        $product = Product::byUser($request->user()->id)->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        }

        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produit supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechercher par code-barres - Basé sur la logique frontend
     */
    public function searchByBarcode(Request $request, $barcode)
    {
        $product = Product::byUser($request->user()->id)
                         ->where('barcode', $barcode)
                         ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé avec ce code-barres'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'sellPrice' => $product->sell_price,
                'stock' => $product->stock,
                'category' => $product->category
            ]
        ]);
    }

    /**
     * Produits en rupture de stock
     */
    public function lowStock(Request $request)
    {
        $threshold = $request->get('threshold', 5);
        
        $products = Product::byUser($request->user()->id)
                          ->lowStock($threshold)
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'category' => $product->category
                ];
            })
        ]);
    }
}
