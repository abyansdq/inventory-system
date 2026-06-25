<?php
// app/Http/Controllers/Admin/StockInController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockInRequest;
use App\Models\Item;
use App\Models\Procurement;
use App\Models\StockIn;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = StockIn::with(['item', 'supplier', 'user']);

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_dokumen', 'like', "%{$search}%")
                  ->orWhereHas('item', fn($q2) =>
                      $q2->where('nama_barang', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $stockIns = $query->latest('tanggal')->paginate(15)->withQueryString();
        $items    = Item::active()->orderBy('nama_barang')->get();

        return view('admin.stock-ins.index', compact('stockIns', 'items'));
    }

    public function create()
    {
        $items       = Item::active()->with('supplier')->orderBy('nama_barang')->get();
        $suppliers   = Supplier::active()->orderBy('nama_supplier')->get();
        $procurements = Procurement::where('status', 'approved')
            ->whereDoesntHave('stockIn')
            ->with(['item', 'supplier'])
            ->get();

        return view('admin.stock-ins.create', compact('items', 'suppliers', 'procurements'));
    }

    public function store(StockInRequest $request)
    {
        $stockIn = $this->stockService->processStockIn(
            $request->validated(),
            auth()->id()
        );

        return redirect()->route('admin.stock-ins.index')
            ->with('success', "Barang masuk {$stockIn->no_dokumen} berhasil dicatat. "
                . "Stok bertambah {$stockIn->qty} unit.");
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['item', 'supplier', 'user', 'procurement']);
        return view('admin.stock-ins.show', compact('stockIn'));
    }

    public function edit(StockIn $stockIn)
    {
        // Stok tidak boleh diedit jika sudah > 1 hari
        if ($stockIn->created_at->diffInDays(now()) > 1) {
            return back()->with('error', 'Transaksi barang masuk tidak dapat diedit setelah 24 jam.');
        }

        $items     = Item::active()->orderBy('nama_barang')->get();
        $suppliers = Supplier::active()->orderBy('nama_supplier')->get();
        return view('admin.stock-ins.edit', compact('stockIn', 'items', 'suppliers'));
    }

    public function update(StockInRequest $request, StockIn $stockIn)
    {
        // Kembalikan stok lama
        $stockIn->item->decrement('stok', $stockIn->qty);

        // Update record
        $stockIn->update($request->validated());

        // Tambah stok baru
        $stockIn->item->increment('stok', $request->qty);

        return redirect()->route('admin.stock-ins.index')
            ->with('success', 'Barang masuk berhasil diperbarui.');
    }

    public function destroy(StockIn $stockIn)
    {
        if ($stockIn->created_at->diffInDays(now()) > 1) {
            return back()->with('error', 'Transaksi tidak dapat dihapus setelah 24 jam.');
        }

        // Kurangi stok balik
        $stockIn->item->decrement('stok', $stockIn->qty);
        $stockIn->delete();

        return redirect()->route('admin.stock-ins.index')
            ->with('success', 'Data barang masuk berhasil dihapus.');
    }
}