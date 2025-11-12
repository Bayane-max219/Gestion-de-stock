<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;

class StoreAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Skip middleware for admin users
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        // Get store ID from request
        $storeId = $request->route('store')
            ?? $request->input('store_id')
            ?? $request->input('from_store_id')
            ?? $request->input('to_store_id');

        // If no store ID is present, continue
        if (!$storeId) {
            return $next($request);
        }

        // Check if user has access to the store
        if (!$request->user()->hasStoreAccess($storeId)) {
            throw new AuthorizationException('You do not have access to this store.');
        }

        return $next($request);
    }
}