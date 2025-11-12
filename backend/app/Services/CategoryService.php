<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getPaginatedCategories(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage);
    }

    public function createCategory(array $data): Category
    {
        // Check if category with same name exists
        if ($this->categoryRepository->findByName($data['name'])) {
            throw ValidationException::withMessages([
                'name' => ['A category with this name already exists.']
            ]);
        }

        return $this->categoryRepository->create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        // Check if another category with the same name exists
        $existingCategory = $this->categoryRepository->findByName($data['name']);
        if ($existingCategory && $existingCategory->id !== $category->id) {
            throw ValidationException::withMessages([
                'name' => ['A category with this name already exists.']
            ]);
        }

        $this->categoryRepository->update($category, $data);
        return $category->fresh();
    }

    public function deleteCategory(Category $category): bool
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            throw ValidationException::withMessages([
                'category' => ['Cannot delete category that has products. Please remove or reassign the products first.']
            ]);
        }

        return $this->categoryRepository->delete($category);
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function getCategoriesWithProductCount(): Collection
    {
        return $this->categoryRepository->getWithProductCount();
    }
}