<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'profile_picture_url' => $this->profile_picture_url,
            'is_active' => $this->is_active,
            'two_factor_enabled' => $this->two_factor_enabled,
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'last_login_ip' => $this->last_login_ip,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                    ];
                });
            }),
            'stores' => $this->whenLoaded('stores', function () {
                return $this->stores->map(function ($store) {
                    return [
                        'id' => $store->id,
                        'name' => $store->name,
                    ];
                });
            }),
            'permissions' => $this->whenLoaded('roles', function () {
                return $this->roles->flatMap(function ($role) {
                    return $role->permissions->pluck('name');
                })->unique()->values();
            }),
        ];
    }
}