<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Exports\ActivityLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->when($request->filled('action'), function ($q) use ($request) {
                $q->where('action', $request->action);
            })
            ->when($request->filled('entity_type'), function ($q) use ($request) {
                $q->where('entity_type', $request->entity_type);
            })
            ->when($request->filled('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%");
            });

        return $query->latest()->paginate($request->per_page ?? 25);
    }

    public function export(Request $request)
    {
        $filename = 'activity-logs-' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new ActivityLogsExport($request), $filename);
    }

    public function show(ActivityLog $log)
    {
        return $log->load('user');
    }

    public function entityTypes()
    {
        return ActivityLog::distinct('entity_type')
            ->pluck('entity_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });
    }

    public function actions()
    {
        return ActivityLog::distinct('action')
            ->pluck('action')
            ->map(function ($action) {
                return [
                    'value' => $action,
                    'label' => ucfirst($action)
                ];
            });
    }
}