<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ActivityLogsExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return ActivityLog::with('user')
            ->when($this->request->filled('action'), function ($q) {
                $q->where('action', $this->request->action);
            })
            ->when($this->request->filled('entity_type'), function ($q) {
                $q->where('entity_type', $this->request->entity_type);
            })
            ->when($this->request->filled('user_id'), function ($q) {
                $q->where('user_id', $this->request->user_id);
            })
            ->when($this->request->filled('start_date'), function ($q) {
                $q->whereDate('created_at', '>=', $this->request->start_date);
            })
            ->when($this->request->filled('end_date'), function ($q) {
                $q->whereDate('created_at', '<=', $this->request->end_date);
            })
            ->when($this->request->filled('search'), function ($q) {
                $q->where('description', 'like', "%{$this->request->search}%");
            });
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->created_at->format('Y-m-d H:i:s'),
            $log->user ? $log->user->name : 'System',
            class_basename($log->entity_type),
            ucfirst($log->action),
            $log->description,
            $log->ip_address,
            json_encode($log->old_values),
            json_encode($log->new_values),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Timestamp',
            'User',
            'Entity Type',
            'Action',
            'Description',
            'IP Address',
            'Old Values',
            'New Values',
        ];
    }
}