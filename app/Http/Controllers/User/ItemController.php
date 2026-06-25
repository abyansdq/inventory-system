<?php
// app/Http/Controllers/User/ItemController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'supplier'])->active();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status_stok')) {
            match($request->status_stok) {
                'tersedia' => $query->where('stok', '>', 0),
                'menipis'  => $query->lowStock(),
                'habis'    => $query->where('stok', 0),
                default    => null,
            };
        }

        $items      = $query->orderBy('nama_barang')->paginate(12)->withQueryString();
        $categories = Category::active()->get();

        return view('user.items.index', compact('items', 'categories'));
    }

    public function show(Item $item)
    {
        $item->load(['category', 'supplier']);

        // Histori pergerakan stok item ini (30 hari)
        $recentStockIns  = $item->stockIns()
            ->whereDate('tanggal', '>=', now()->subDays(30))
            ->latest('tanggal')
            ->limit(5)
            ->get();

        $recentStockOuts = $item->stockOuts()
            ->whereDate('tanggal', '>=', now()->subDays(30))
            ->latest('tanggal')
            ->limit(5)
            ->get();

        // Forecast terbaru
        $latestForecast = $item->forecasts()
            ->where('metode', 'weighted_moving_average')
            ->latest()
            ->first();

        return view('user.items.show', compact(
            'item', 'recentStockIns', 'recentStockOuts', 'latestForecast'
        ));
    }
}