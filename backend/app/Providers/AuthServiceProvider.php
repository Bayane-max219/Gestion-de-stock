<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\StockMovementPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Product::class => ProductPolicy::class,
        Category::class => CategoryPolicy::class,
        StockMovement::class => StockMovementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for roles
        Gate::define('admin', fn(User $user) => $user->hasRole('admin'));
        Gate::define('magasinier', fn(User $user) => $user->hasRole(['admin', 'magasinier']));
        Gate::define('commercial', fn(User $user) => $user->hasRole(['admin', 'commercial']));
        Gate::define('caissier', fn(User $user) => $user->hasRole(['admin', 'caissier']));

        // Additional gates for store access
        Gate::define('access-store', function (User $user, $storeId) {
            return $user->hasRole('admin') || $user->stores->contains($storeId);
        });
    }
}