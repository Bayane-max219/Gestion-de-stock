<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            static::logActivity('create', $model);
        });

        static::updated(function (Model $model) {
            $changes = $model->getDirty();
            $original = Arr::only($model->getOriginal(), array_keys($changes));
            
            if (!empty($changes)) {
                static::logActivity('update', $model, null, $original, $changes);
            }
        });

        static::deleted(function (Model $model) {
            static::logActivity('delete', $model);
        });
    }

    protected static function logActivity($action, $model, $description = null, $oldValues = null, $newValues = null)
    {
        // Skip logging if we're running tests or seeding
        if (app()->environment('testing') || app()->runningInConsole()) {
            return;
        }

        ActivityLog::log($action, $model, $description, $oldValues, $newValues);
    }

    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'entity');
    }
}