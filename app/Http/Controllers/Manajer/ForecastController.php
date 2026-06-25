<?php
// app/Http/Controllers/Manajer/ForecastController.php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Forecast;
use App\Models\Item;
use App\Services\ForecastService;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    public function __construct(private ForecastService $forecastService) {}

    public function index(Request $request)
    {
        $query = Item::with([
            'category',
            'forecasts' => fn($q) => $q
                ->where('metode', 'weighted_moving_average')
                ->latest()->limit(1),
        ])->active();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items      = $query->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        return view('manajer.forecasts.index', compact('items', 'categories'));
    }

    public function show(Item $item)
    {
        $item->load(['category', 'supplier']);

        $demandHistories = $item->demandHistories()
            ->orderByPeriode()
            ->get();

        $forecasts = Forecast::where('item_id', $item->id)
            ->where('metode', 'weighted_moving_average')
            ->orderBy('tahun_prediksi')
            ->orderBy('bulan_prediksi')
            ->get();

        $chartData   = $this->forecastService->getChartData($item);
        $accuracy    = $this->forecastService->calculateAccuracy($item);
        $hasForecast = $forecasts->isNotEmpty();

        return view('manajer.forecasts.show', compact(
            'item', 'demandHistories', 'forecasts',
            'chartData', 'accuracy', 'hasForecast'
        ));
    }
}