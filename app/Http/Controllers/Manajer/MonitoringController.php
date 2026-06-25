<?php
// app/Http/Controllers/Manajer/MonitoringController.php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $lowStockItems = Item::with(['category', 'supplier'])
            ->active()
            ->lowStock()
            ->orderBy('stok')
            ->get();

        $outOfStockItems = Item::with(['category'])
            ->active()
            ->where('stok', 0)
            ->get();

        $safeCount = Item::active()
            ->where('stok', '>', 0)
            ->whereColumn('stok', '>', 'stok_minimum')
            ->count();

        $summary = [
            'total_barang'  => Item::active()->count(),
            'stok_aman'     => $safeCount,
            'stok_menipis'  => $lowStockItems->count(),
            'stok_habis'    => $outOfStockItems->count(),
        ];

        // Data grafik 30 hari
        $labels = [];
        $masuk  = [];
        $keluar = [];

        for ($i = 29; $i >= 0; $i--) {
            $date     = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('d/m');
            $masuk[]  = StockIn::whereDate('tanggal', $date)->sum('qty')  ?? 0;
            $keluar[] = StockOut::whereDate('tanggal', $date)->sum('qty') ?? 0;
        }

        $movementData = compact('labels', 'masuk', 'keluar');

        // Top 5 barang keluar
        $topItems = StockOut::selectRaw('item_id, SUM(qty) as total_keluar')
            ->whereDate('tanggal', '>=', now()->subDays(30))
            ->groupBy('item_id')
            ->orderByDesc('total_keluar')
            ->with('item')
            ->limit(5)
            ->get();

        return view('manajer.monitoring.index', compact(
            'lowStockItems', 'outOfStockItems',
            'summary', 'movementData', 'topItems'
        ));
    }
}