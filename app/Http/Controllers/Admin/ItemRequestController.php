<?php
// app/Http/Controllers/Admin/ItemRequestController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemRequest;
use App\Services\StockService;
use Illuminate\Http\Request;

class ItemRequestController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = ItemRequest::with(['user', 'item.category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_permintaan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('item', fn($q2) => $q2->where('nama_barang', 'like', "%{$search}%"));
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'pending'   => ItemRequest::pending()->count(),
            'approved'  => ItemRequest::approved()->count(),
            'rejected'  => ItemRequest::where('status', 'rejected')->count(),
            'processed' => ItemRequest::where('status', 'processed')->count(),
        ];

        return view('admin.item-requests.index', compact('requests', 'stats'));
    }

    public function show(ItemRequest $itemRequest)
    {
        $itemRequest->load(['user', 'item.category', 'approvedBy', 'stockOut']);
        return view('admin.item-requests.show', compact('itemRequest'));
    }

    public function approve(Request $request, ItemRequest $itemRequest)
    {
        if ($itemRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        try {
            $this->stockService->approveAndProcessRequest(
                $itemRequest,
                auth()->id(),
                $request->catatan_admin
            );

            return back()->with('success',
                "Permintaan {$itemRequest->no_permintaan} disetujui dan barang keluar diproses."
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, ItemRequest $itemRequest)
    {
        $request->validate([
            'catatan_admin' => ['required', 'string', 'max:500'],
        ], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
        ]);

        if ($itemRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $this->stockService->rejectRequest(
            $itemRequest,
            auth()->id(),
            $request->catatan_admin
        );

        return back()->with('success', "Permintaan {$itemRequest->no_permintaan} ditolak.");
    }

    public function process(ItemRequest $itemRequest)
    {
        if ($itemRequest->status !== 'approved') {
            return back()->with('error', 'Hanya permintaan yang sudah disetujui yang bisa diproses.');
        }

        try {
            $this->stockService->processStockOut([
                'item_id'         => $itemRequest->item_id,
                'item_request_id' => $itemRequest->id,
                'qty'             => $itemRequest->qty,
                'tanggal'         => today(),
                'keterangan'      => "Proses dari {$itemRequest->no_permintaan}",
            ], auth()->id());

            return back()->with('success', 'Barang berhasil diproses keluar.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}