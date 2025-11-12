<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        // Only admin can delete suppliers
        return $user->hasRole('admin');
    }

    public function import(User $user): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }
}