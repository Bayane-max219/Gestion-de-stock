<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StoreScopeScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (\Illuminate\Support\Facades\Auth::check() && !\Illuminate\Support\Facades\Auth::user()->isAdmin()) {
            $builder->whereIn('store_id', \Illuminate\Support\Facades\Auth::user()->getAccessibleStoreIds());
        }
    }
}