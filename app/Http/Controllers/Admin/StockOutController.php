<?php
// app/Http/Controllers/Admin/StockOutController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockOutRequest;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\StockOut;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = StockOut::with(['item', 'user', 'itemRequest']);

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

        $stockOuts = $query->latest('tanggal')->paginate(15)->withQueryString();
        $items     = Item::active()->orderBy('nama_barang')->get();

        return view('admin.stock-outs.index', compact('stockOuts', 'items'));
    }

    public function create()
    {
        $items        = Item::active()->where('stok', '>', 0)
                            ->orderBy('nama_barang')->get();
        $itemRequests = ItemRequest::where('status', 'approved')
                            ->whereDoesntHave('stockOut')
                            ->with('item')
                            ->get();

        return view('admin.stock-outs.create', compact('items', 'itemRequests'));
    }

    public function store(StockOutRequest $request)
    {
        try {
            $stockOut = $this->stockService->processStockOut(
                $request->validated(),
                auth()->id()
            );

            return redirect()->route('admin.stock-outs.index')
                ->with('success',
                    "Barang keluar {$stockOut->no_dokumen} berhasil dicatat."
                );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(StockOut $stockOut)
    {
        $stockOut->load(['item', 'user', 'itemRequest.user']);
        return view('admin.stock-outs.show', compact('stockOut'));
    }

    public function edit(StockOut $stockOut)
    {
        abort(403, 'Transaksi barang keluar tidak dapat diedit.');
    }

    public function update(StockOutRequest $request, StockOut $stockOut)
    {
        abort(403, 'Transaksi barang keluar tidak dapat diedit.');
    }

    public function destroy(StockOut $stockOut)
    {
        if ($stockOut->created_at->diffInDays(now()) > 1) {
            return back()->with('error',
                'Transaksi tidak dapat dihapus setelah 24 jam.'
            );
        }

        // Kembalikan stok
        $stockOut->item->increment('stok', $stockOut->qty);
        $stockOut->delete();

        return redirect()->route('admin.stock-outs.index')
            ->with('success', 'Data barang keluar berhasil dihapus.');
    }
}