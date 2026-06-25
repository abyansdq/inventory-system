<?php
// app/Http/Controllers/Manajer/EoqController.php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EoqCalculation;
use App\Models\Item;
use App\Services\EoqService;
use App\Exceptions\InsufficientDataException;
use Illuminate\Http\Request;

class EoqController extends Controller
{
    public function __construct(private EoqService $eoqService) {}

    public function index(Request $request)
    {
        $query = Item::with([
            'category',
            'eoqCalculations' => fn($q) =>
                $q->latest('tanggal_hitung')->limit(1),
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

        return view('manajer.eoq.index', compact('items', 'categories'));
    }

    public function show(Item $item)
    {
        $item->load(['category', 'supplier']);

        $calculations = EoqCalculation::where('item_id', $item->id)
            ->with('calculatedBy')
            ->latest('tanggal_hitung')
            ->paginate(10);

        $latest  = $this->eoqService->getLatestResult($item);
        $summary = $latest ? $this->eoqService->getSummary($latest) : null;

        $demandHistories = $item->demandHistories()
            ->orderByPeriode()
            ->get();

        $demandData = null;
        try {
            $demandData = $this->eoqService->getDemandData($item);
        } catch (InsufficientDataException $e) {
            // Belum cukup data
        }

        return view('manajer.eoq.show', compact(
            'item', 'calculations', 'latest',
            'summary', 'demandHistories', 'demandData'
        ));
    }
}