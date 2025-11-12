<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Everyone can view categories
    }

    public function view(User $user, Category $category): bool
    {
        return true; // Everyone can view individual categories
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Category $category): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }

        // Prevent deletion of categories with products
        return $category->products()->count() === 0;
    }
}