<?php
// app/Http/Controllers/Manajer/ItemController.php

namespace App\Http\Controllers\Manajer;

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
                'menipis' => $query->lowStock(),
                'habis'   => $query->where('stok', 0),
                'aman'    => $query->where('stok', '>', 0)
                                   ->whereColumn('stok', '>', 'stok_minimum'),
                default   => null,
            };
        }

        $items      = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        return view('manajer.items.index', compact('items', 'categories'));
    }

    public function show(Item $item)
    {
        $item->load(['category', 'supplier']);

        // EOQ terbaru
        $latestEoq = $item->eoqCalculations()
            ->latest('tanggal_hitung')
            ->first();

        // Forecast terbaru
        $latestForecast = $item->forecasts()
            ->where('metode', 'weighted_moving_average')
            ->latest()
            ->first();

        // Ringkasan stok
        $summary = [
            'stok_saat_ini'    => $item->stok,
            'stok_minimum'     => $item->stok_minimum,
            'safety_stock'     => $item->safety_stock,
            'status_stok'      => $item->status_stok,
            'masuk_bulan_ini'  => $item->stockIns()
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('qty'),
            'keluar_bulan_ini' => $item->stockOuts()
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('qty'),
            'nilai_stok'       => $item->stok * $item->harga_beli,
        ];

        return view('manajer.items.show', compact(
            'item', 'latestEoq', 'latestForecast', 'summary'
        ));
    }
}