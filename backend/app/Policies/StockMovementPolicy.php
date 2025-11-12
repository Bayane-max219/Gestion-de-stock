<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }

    public function view(User $user, StockMovement $movement): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Warehouse staff can only see movements in their assigned stores
        if ($user->hasRole('magasinier')) {
            return $user->stores->contains($movement->store_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }

    public function update(User $user, StockMovement $movement): bool
    {
        // Stock movements should not be editable once created
        return false;
    }

    public function delete(User $user, StockMovement $movement): bool
    {
        // Stock movements should not be deletable once created
        return false;
    }

    public function transfer(User $user): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }

    public function adjust(User $user): bool
    {
        return $user->hasRole(['admin', 'magasinier']);
    }
}