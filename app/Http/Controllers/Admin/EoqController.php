<?php
// app/Http/Controllers/Admin/EoqController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\EoqCalculation;
use App\Services\EoqService;
use App\Exceptions\InsufficientDataException;
use Illuminate\Http\Request;

class EoqController extends Controller
{
    public function __construct(private EoqService $eoqService) {}

    // -------------------------------------------------------
    // Daftar semua barang + status EOQ terakhir
    // -------------------------------------------------------
    public function index(Request $request)
    {
        $query = Item::with([
            'category',
            'eoqCalculations' => fn($q) => $q->latest('tanggal_hitung')->limit(1),
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
        $categories = \App\Models\Category::active()->get();

        return view('admin.eoq.index', compact('items', 'categories'));
    }

    // -------------------------------------------------------
    // Detail EOQ satu barang + riwayat kalkulasi
    // -------------------------------------------------------
    public function show(Item $item)
    {
        $item->load(['category', 'supplier']);

        // Riwayat kalkulasi
        $calculations = EoqCalculation::where('item_id', $item->id)
            ->with('calculatedBy')
            ->latest('tanggal_hitung')
            ->paginate(10);

        // Kalkulasi terbaru
        $latest = $this->eoqService->getLatestResult($item);
        $summary = $latest ? $this->eoqService->getSummary($latest) : null;

        // Data histori demand untuk ditampilkan
        $demandHistories = $item->demandHistories()
            ->orderByPeriode()
            ->get();

        // Coba ambil demand data (untuk info parameter)
        $demandData = null;
        try {
            $demandData = $this->eoqService->getDemandData($item);
        } catch (InsufficientDataException $e) {
            // Belum cukup data
        }

        return view('admin.eoq.show', compact(
            'item', 'calculations', 'latest',
            'summary', 'demandHistories', 'demandData'
        ));
    }

    // -------------------------------------------------------
    // Proses kalkulasi EOQ
    // -------------------------------------------------------
    public function calculate(Request $request, Item $item)
    {
        $request->validate([
            'ordering_cost'  => ['nullable', 'numeric', 'min:1'],
            'holding_cost'   => ['nullable', 'numeric', 'min:1'],
            'lead_time'      => ['nullable', 'integer', 'min:1'],
            'demand_tahunan' => ['nullable', 'numeric', 'min:1'],
            'keterangan'     => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $params = array_filter([
                'ordering_cost'  => $request->ordering_cost,
                'holding_cost'   => $request->holding_cost,
                'lead_time'      => $request->lead_time
                    ? (int) $request->lead_time : null,
                'demand_tahunan' => $request->demand_tahunan,
                'keterangan'     => $request->keterangan,
            ], fn($v) => !is_null($v));

            $result = $this->eoqService->calculate($item, $params, auth()->id());

            return redirect()
                ->route('admin.eoq.show', $item)
                ->with('success',
                    "EOQ berhasil dihitung! "
                    . "EOQ = <strong>" . number_format($result->eoq_result, 2) . "</strong> unit, "
                    . "ROP = <strong>" . number_format($result->rop_result, 2) . "</strong> unit."
                );

        } catch (InsufficientDataException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}