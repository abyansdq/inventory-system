<?php
// app/Http/Controllers/User/ItemRequestController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ItemRequestFormRequest;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Services\DocumentNumberService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ItemRequestController extends Controller
{
    public function __construct(
        private DocumentNumberService $docNumber,
        private NotificationService   $notification
    ) {}

    public function index(Request $request)
    {
        $requests = ItemRequest::with(['item'])
            ->where('user_id', auth()->id())
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10);

        return view('user.item-requests.index', compact('requests'));
    }

    public function create()
    {
        $items = Item::active()->where('stok', '>', 0)->with('category')->get();
        return view('user.item-requests.create', compact('items'));
    }

    public function store(ItemRequestFormRequest $request)
    {
        $itemRequest = ItemRequest::create([
            'no_permintaan' => $this->docNumber->forItemRequest(),
            'user_id'       => auth()->id(),
            'item_id'       => $request->item_id,
            'qty'           => $request->qty,
            'tanggal'       => today(),
            'tanggal_butuh' => $request->tanggal_butuh,
            'keperluan'     => $request->keperluan,
            'status'        => 'pending',
        ]);

        // Notifikasi ke Admin
        $this->notification->sendNewRequestNotification($itemRequest);

        return redirect()->route('user.item-requests.index')
            ->with('success', "Permintaan {$itemRequest->no_permintaan} berhasil diajukan.");
    }

    public function show(ItemRequest $itemRequest)
    {
        // Pastikan hanya pemilik yang bisa lihat
        abort_unless($itemRequest->user_id === auth()->id(), 403);
        $itemRequest->load(['item', 'approvedBy']);
        return view('user.item-requests.show', compact('itemRequest'));
    }

    public function cancel(ItemRequest $itemRequest)
    {
        abort_unless($itemRequest->user_id === auth()->id(), 403);

        if (!in_array($itemRequest->status, ['pending'])) {
            return back()->with('error', 'Permintaan tidak dapat dibatalkan.');
        }

        $itemRequest->update(['status' => 'cancelled']);

        return back()->with('success', 'Permintaan berhasil dibatalkan.');
    }
}