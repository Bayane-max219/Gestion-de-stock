<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 15);
        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->get('all')) {
            $categories = $query->with(['parent', 'children'])->orderBy('name')->get();
        } else {
            $categories = $query->with(['parent', 'children'])->orderBy('name')->paginate($perPage);
        }

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $data = $request->validated();
        
        // Validate parent_id circular reference
        if (!empty($data['parent_id'])) {
            $this->validateParentId($data['parent_id']);
        }

        $category = $this->categoryService->createCategory($data);
        return new CategoryResource($category->load(['parent', 'children']));
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->load(['parent', 'children', 'products']));
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $data = $request->validated();
        
        // Validate parent_id circular reference
        if (!empty($data['parent_id'])) {
            $this->validateParentId($data['parent_id'], $category);
        }

        $category = $this->categoryService->updateCategory($category, $data);
        return new CategoryResource($category->load(['parent', 'children']));
    }

    public function destroy(Category $category): JsonResponse
    {
        // Move children categories to parent if exists
        if ($category->children()->exists()) {
            $category->children()->update(['parent_id' => $category->parent_id]);
        }

        $this->categoryService->deleteCategory($category);
        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function tree(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')
            ->with('allChildren')
            ->get();

        return response()->json(CategoryResource::collection($categories));
    }

    public function withProductCount(): AnonymousResourceCollection
    {
        $categories = $this->categoryService->getCategoriesWithProductCount();
        return CategoryResource::collection($categories);
    }

    protected function validateParentId($parentId, ?Category $category = null): void
    {
        if ($category && $parentId == $category->id) {
            throw ValidationException::withMessages([
                'parent_id' => ['A category cannot be its own parent.']
            ]);
        }

        $parent = Category::find($parentId);
        while ($parent) {
            if ($category && $parent->id === $category->id) {
                throw ValidationException::withMessages([
                    'parent_id' => ['Cannot create circular reference in category hierarchy']
                ]);
            }
            $parent = $parent->parent;
        }
    }
}