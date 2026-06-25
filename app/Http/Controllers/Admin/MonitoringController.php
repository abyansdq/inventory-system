<?php
// app/Http/Controllers/Admin/MonitoringController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        // Semua stok menipis
        $lowStockItems = Item::with(['category', 'supplier'])
            ->active()
            ->lowStock()
            ->orderBy('stok')
            ->get();

        // Stok habis
        $outOfStockItems = Item::with(['category'])
            ->active()
            ->where('stok', 0)
            ->get();

        // Stok aman
        $safeItems = Item::active()
            ->where('stok', '>', 0)
            ->whereColumn('stok', '>', 'stok_minimum')
            ->count();

        // Summary
        $summary = [
            'total_barang'   => Item::active()->count(),
            'stok_aman'      => $safeItems,
            'stok_menipis'   => $lowStockItems->count(),
            'stok_habis'     => $outOfStockItems->count(),
        ];

        // Pergerakan 30 hari
        $movementData = $this->getMovement30Days();

        // Top 5 barang paling sering keluar
        $topItems = StockOut::selectRaw('item_id, SUM(qty) as total_keluar')
            ->whereDate('tanggal', '>=', now()->subDays(30))
            ->groupBy('item_id')
            ->orderByDesc('total_keluar')
            ->with('item')
            ->limit(5)
            ->get();

        return view('admin.monitoring.index', compact(
            'lowStockItems', 'outOfStockItems',
            'summary', 'movementData', 'topItems'
        ));
    }

    private function getMovement30Days(): array
    {
        $labels = [];
        $masuk  = [];
        $keluar = [];

        for ($i = 29; $i >= 0; $i--) {
            $date     = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('d/m');
            $masuk[]  = StockIn::whereDate('tanggal', $date)->sum('qty')  ?? 0;
            $keluar[] = StockOut::whereDate('tanggal', $date)->sum('qty') ?? 0;
        }

        return compact('labels', 'masuk', 'keluar');
    }
}