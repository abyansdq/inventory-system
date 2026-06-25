<?php
// app/Http/Controllers/Manajer/ItemRequestController.php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\ItemRequest;
use Illuminate\Http\Request;

class ItemRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemRequest::with(['user', 'item.category', 'approvedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_permintaan', 'like', "%{$search}%")
                  ->orWhereHas('item', fn($q2) =>
                      $q2->where('nama_barang', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($q2) =>
                      $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'pending'   => ItemRequest::pending()->count(),
            'approved'  => ItemRequest::approved()->count(),
            'processed' => ItemRequest::where('status', 'processed')->count(),
            'rejected'  => ItemRequest::where('status', 'rejected')->count(),
        ];

        return view('manajer.item-requests.index', compact('requests', 'stats'));
    }

    public function show(ItemRequest $itemRequest)
    {
        $itemRequest->load(['user', 'item.category', 'approvedBy', 'stockOut']);
        return view('manajer.item-requests.show', compact('itemRequest'));
    }
}