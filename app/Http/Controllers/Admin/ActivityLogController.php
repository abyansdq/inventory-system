<?php
// app/Http/Controllers/Admin/ActivityLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        if ($search = $request->search) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id)
                  ->where('causer_type', \App\Models\User::class);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $logs    = $query->paginate(20)->withQueryString();
        $users   = User::orderBy('name')->get();
        $logNames = Activity::distinct()->pluck('log_name')->filter()->sort()->values();

        return view('admin.activity-logs.index', compact(
            'logs', 'users', 'logNames'
        ));
    }
}