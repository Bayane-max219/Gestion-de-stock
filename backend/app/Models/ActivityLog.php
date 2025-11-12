<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }

    public static function log($action, $entity, $description = null, $oldValues = null, $newValues = null)
    {
        $user = auth()->user();
        $request = request();

        return static::create([
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
            'description' => $description ?? static::generateDescription($action, $entity),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    protected static function generateDescription($action, $entity): string
    {
        $entityName = class_basename($entity);
        $identifier = $entity->name ?? $entity->id;

        return match ($action) {
            'create' => "Created new {$entityName}: {$identifier}",
            'update' => "Updated {$entityName}: {$identifier}",
            'delete' => "Deleted {$entityName}: {$identifier}",
            default => "{$action} {$entityName}: {$identifier}",
        };
    }
}