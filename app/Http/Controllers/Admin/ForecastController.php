<?php
// app/Http/Controllers/Admin/ForecastController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Forecast;
use App\Services\ForecastService;
use App\Exceptions\InsufficientDataException;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    public function __construct(private ForecastService $forecastService) {}

    public function index(Request $request)
    {
        $query = Item::with([
            'forecasts' => fn($q) => $q
                ->where('metode', 'weighted_moving_average')
                ->latest()
                ->limit(1),
            'category',
        ])->active();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(15)->withQueryString();

        return view('admin.forecasts.index', compact('items'));
    }

    public function show(Item $item)
    {
        $item->load(['category', 'supplier']);

        // Histori demand
        $demandHistories = $item->demandHistories()
            ->orderByPeriode()
            ->get();

        // Semua forecast
        $forecasts = Forecast::where('item_id', $item->id)
            ->where('metode', 'weighted_moving_average')
            ->orderBy('tahun_prediksi')
            ->orderBy('bulan_prediksi')
            ->get();

        // Data chart
        $chartData = $this->forecastService->getChartData($item);

        // Akurasi
        $accuracy = $this->forecastService->calculateAccuracy($item);

        // Cek data cukup
        $dataCount  = $demandHistories->count();
        $cukupData  = $dataCount >= 3;

        return view('admin.forecasts.show', compact(
            'item', 'demandHistories', 'forecasts',
            'chartData', 'accuracy', 'cukupData', 'dataCount'
        ));
    }

    public function generate(Request $request, Item $item)
    {
        $request->validate([
            'n'             => ['required', 'integer', 'between:2,6'],
            'bulan_kedepan' => ['required', 'integer', 'between:1,6'],
            'bobot'         => ['nullable', 'string'],
        ]);

        try {
            $n    = (int) $request->n;
            $bobot = null;

            // Parse bobot custom jika ada
            if ($request->bobot) {
                $bobot = array_map(
                    'floatval',
                    explode(',', $request->bobot)
                );
            }

            $results = $this->forecastService->generateMultiple(
                $item,
                (int) $request->bulan_kedepan,
                $n,
                auth()->id()
            );

            $count = count($results);
            return redirect()
                ->route('admin.forecasts.show', $item)
                ->with('success',
                    "Berhasil generate <strong>{$count} prediksi</strong> "
                    . "menggunakan WMA {$n} periode."
                );

        } catch (InsufficientDataException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}