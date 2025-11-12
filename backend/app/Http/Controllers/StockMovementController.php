<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Http\Requests\StockTransferRequest;
use App\Http\Resources\StockMovementResource;
use App\Http\Resources\StockSummaryResource;
use App\Services\StockMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockMovementController extends Controller
{
    protected $stockMovementService;

    public function __construct(StockMovementService $stockMovementService)
    {
        $this->stockMovementService = $stockMovementService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Get paginated list of stock movements
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', StockMovement::class);
        
        $movements = $this->stockMovementService->getAllMovements($request->all());
        return StockMovementResource::collection($movements);
    }

    /**
     * Get stock movements for a specific product
     */
    public function productMovements(Request $request, int $productId): AnonymousResourceCollection
    {
        $this->authorize('view', Product::findOrFail($productId));
        
        $movements = $this->stockMovementService->getProductMovements($productId, $request->all());
        return StockMovementResource::collection($movements);
    }

    /**
     * Get stock movements for a specific store
     */
    public function storeMovements(Request $request, int $storeId): AnonymousResourceCollection
    {
        $this->authorize('view', Store::findOrFail($storeId));
        
        $movements = $this->stockMovementService->getStoreMovements($storeId, $request->all());
        return StockMovementResource::collection($movements);
    }

    /**
     * Adjust stock level for a product in a store
     */
    public function adjust(StockAdjustmentRequest $request): JsonResponse
    {
        $this->authorize('create', StockMovement::class);
        
        $result = $this->stockMovementService->adjustStock($request->validated());
        return response()->json([
            'message' => 'Stock adjusted successfully',
            'data' => new StockMovementResource($result['movement']),
            'previous_stock' => $result['previous_stock'],
            'new_stock' => $result['new_stock']
        ]);
    }

    /**
     * Transfer stock between stores
     */
    public function transfer(StockTransferRequest $request): JsonResponse
    {
        $this->authorize('create', StockMovement::class);
        
        $result = $this->stockMovementService->transferStock($request->validated());
        return response()->json([
            'message' => 'Stock transferred successfully',
            'data' => $result
        ]);
    }

    /**
     * Get stock summary with optional store breakdown
     */
    public function summary(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', StockMovement::class);
        
        $summary = $this->stockMovementService->getStockSummary($request->all());
        return StockSummaryResource::collection($summary);
    }
}