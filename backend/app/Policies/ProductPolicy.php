<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Everyone can view products list
    }

    public function view(User $user, Product $product): bool
    {
        return true; // Everyone can view individual products
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole('magasinier');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->hasRole('magasinier');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function updateStock(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->hasRole('magasinier');
    }

    public function import(User $user): bool
    {
        return $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole('magasinier');
    }

    public function manageStores(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->hasRole('magasinier');
    }
}