<?php
// app/Http/Controllers/Admin/ProcurementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procurement;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index(Request $request)
    {
        $query = Procurement::with(['item', 'supplier', 'user', 'approvedBy', 'stockIn']);

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_pengadaan', 'like', "%{$search}%")
                  ->orWhereHas('item', fn($q2) =>
                      $q2->where('nama_barang', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $procurements = $query->latest()->paginate(15)->withQueryString();

        return view('admin.procurements.index', compact('procurements'));
    }

    public function show(Procurement $procurement)
    {
        $procurement->load([
            'item.category',
            'supplier',
            'user',
            'approvedBy',
            'stockIn',
        ]);

        return view('admin.procurements.show', compact('procurement'));
    }
}