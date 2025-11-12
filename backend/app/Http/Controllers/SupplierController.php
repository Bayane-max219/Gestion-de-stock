<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\SupplierResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchasesExport;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Supplier::class);
        
        $suppliers = $this->supplierService->getAll($request->all());
        return SupplierResource::collection($suppliers);
    }

    public function store(SupplierStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Supplier::class);
        
        $result = $this->supplierService->create($request->validated());
        return response()->json([
            'message' => $result['message'],
            'supplier' => new SupplierResource($result['supplier'])
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->supplierService->findById($id);
        $this->authorize('view', $result['supplier']);
        
        return response()->json([
            'supplier' => new SupplierResource($result['supplier'])
        ]);
    }

    public function update(SupplierUpdateRequest $request, int $id): JsonResponse
    {
        $this->authorize('update', Supplier::findOrFail($id));
        
        $result = $this->supplierService->update($id, $request->validated());
        return response()->json([
            'message' => $result['message'],
            'supplier' => new SupplierResource($result['supplier'])
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', Supplier::findOrFail($id));
        
        $result = $this->supplierService->delete($id);
        return response()->json(['message' => $result['message']]);
    }

    public function products(Request $request, int $id): AnonymousResourceCollection
    {
        $this->authorize('view', Supplier::findOrFail($id));
        
        $products = $this->supplierService->getProducts($id, $request->all());
        return ProductResource::collection($products);
    }

    public function history(Request $request, int $id)
    {
        $this->authorize('view', Supplier::findOrFail($id));
        
        // If export requested, return Excel export for supplier purchases
        if ($request->boolean('export')) {
            $filters = $request->all();
            $filters['supplier_id'] = $id;
            return Excel::download(new PurchasesExport($filters), "supplier-{$id}-purchases.xlsx");
        }

        $history = $this->supplierService->getPurchaseHistory($id, $request->all());
        return PurchaseResource::collection($history);
    }

    public function import(Request $request): JsonResponse
    {
        $this->authorize('create', Supplier::class);
        
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv']
        ]);

        $result = $this->supplierService->importSuppliers($request->file('file'));
        return response()->json([
            'message' => $result['message'],
            'imported_count' => $result['imported_count'],
            'errors' => $result['errors']
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', Supplier::class);
        
        $filename = $this->supplierService->exportSuppliers($request->all());
        return response()->download(storage_path("app/exports/{$filename}"))
            ->deleteFileAfterSend();
    }

    public function stats(int $id): JsonResponse
    {
        $this->authorize('view', Supplier::findOrFail($id));
        
        $stats = $this->supplierService->getStats($id);
        return response()->json(['stats' => $stats]);
    }
}