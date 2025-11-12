<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only([
            'category_id',
            'supplier_id',
            'search',
            'low_stock',
            'sort_by',
            'sort_direction',
            'per_page'
        ]);

        $products = $this->productService->getPaginatedProducts($filters, $request->get('per_page', 15));
        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $product = $this->productService->createProduct($request->validated());

        // Initialize stock in all stores
        $stores = Store::all();
        foreach ($stores as $store) {
            $product->stores()->attach($store->id, ['quantity' => 0]);
        }

        return new ProductResource($product->load(['category', 'supplier', 'stores']));
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load(['category', 'supplier', 'stores']));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product = $this->productService->updateProduct($product, $request->validated());
        return new ProductResource($product->load(['category', 'supplier', 'stores']));
    }

    public function destroy(Product $product): JsonResponse
    {
        // Check if product can be deleted (no sales or purchases)
        $hasTransactions = DB::table('sale_items')->where('product_id', $product->id)->exists()
            || DB::table('purchase_items')->where('product_id', $product->id)->exists();

        if ($hasTransactions) {
            throw ValidationException::withMessages([
                'product' => ['Cannot delete product with existing transactions']
            ]);
        }

        $this->productService->deleteProduct($product);
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2']
        ]);

        $products = $this->productService->searchProducts(
            $request->query('query'),
            $request->only(['category_id', 'supplier_id', 'min_price', 'max_price', 'in_stock', 'low_stock'])
        );

        return ProductResource::collection($products);
    }

    public function updateStock(Request $request, Product $product): JsonResponse
    {
        $this->authorize('updateStock', $product);

        $request->validate([
            'store_id' => ['required', 'exists:stores,id'],
            'quantity' => ['required', 'integer'],
            'type' => ['required', 'in:add,subtract'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            $newQuantity = $product->updateStock(
                $request->store_id,
                $request->quantity,
                $request->type
            );

            StockMovement::recordMovement(
                $request->store_id,
                $product->id,
                StockMovement::TYPE_ADJUSTMENT,
                $request->type === 'add' ? $request->quantity : -$request->quantity,
                null,
                null,
                $request->notes
            );

            DB::commit();

            return response()->json([
                'message' => 'Stock updated successfully',
                'new_quantity' => $newQuantity
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'quantity' => [$e->getMessage()]
            ]);
        }
    }

    public function lowStock(): AnonymousResourceCollection
    {
        $products = $this->productService->getLowStockProducts();
        return ProductResource::collection($products);
    }

    public function import(Request $request): JsonResponse
    {
        $this->authorize('import', Product::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048']
        ]);

        try {
            $importedCount = Excel::import(new ProductsImport, $request->file('file'));
            return response()->json([
                'message' => "{$importedCount} products imported successfully"
            ]);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'file' => [$e->getMessage()]
            ]);
        }
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('export', Product::class);
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}