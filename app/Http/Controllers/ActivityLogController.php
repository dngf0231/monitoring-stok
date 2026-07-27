<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('activity_logs.index');
    }

    public function datatable(Request $request)
    {
        $columns = ['created_at', 'channel', 'action', 'user_id', 'entity_type', 'description', 'ip_address'];
        $query = ActivityLog::query()->with('user');
        $recordsTotal = ActivityLog::count();
        $search = $request->input('search.value');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('channel', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $recordsFiltered = $query->count();
        $orderIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($columns[$orderIndex] ?? 'created_at', $orderDir);

        $rows = $query->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(fn (ActivityLog $log) => [
                'created_at' => $log->created_at->format('d M Y H:i:s'),
                'channel' => '<span class="badge ' . ($log->channel === 'api' ? 'bg-dark' : 'bg-primary') . '">' . e(strtoupper($log->channel)) . '</span>',
                'action' => '<span class="fw-semibold">' . e($log->action) . '</span>',
                'user' => $log->user ? e($log->user->name) . '<br><small class="text-muted">' . e($log->user->email) . '</small>' : '<span class="text-muted">System/Guest</span>',
                'entity' => $log->entity_type ? e(class_basename($log->entity_type)) . '<br><small class="text-muted">ID: ' . e((string) $log->entity_id) . '</small>' : '-',
                'description' => e($log->description ?? '-'),
                'ip_address' => e($log->ip_address ?? '-'),
            ]);

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }
}
