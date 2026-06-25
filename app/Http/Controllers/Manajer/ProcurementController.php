<?php
// app/Http/Controllers/Manajer/ProcurementController.php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manajer\ProcurementRequest;
use App\Models\Item;
use App\Models\Procurement;
use App\Models\Supplier;
use App\Services\DocumentNumberService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function __construct(
        private DocumentNumberService $docNumber,
        private NotificationService   $notification
    ) {}

    public function index(Request $request)
    {
        $query = Procurement::with(['item', 'supplier', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_pengadaan', 'like', "%{$search}%")
                  ->orWhereHas('item', fn($q2) =>
                      $q2->where('nama_barang', 'like', "%{$search}%"));
            });
        }

        $procurements = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'draft'    => Procurement::where('status', 'draft')->count(),
            'pending'  => Procurement::pending()->count(),
            'approved' => Procurement::approved()->count(),
            'received' => Procurement::where('status', 'received')->count(),
        ];

        return view('manajer.procurements.index', compact('procurements', 'stats'));
    }

    public function create()
    {
        $items     = Item::active()->with(['category', 'eoqCalculations' => fn($q) => $q->latest()->limit(1)])
                         ->orderBy('nama_barang')->get();
        $suppliers = Supplier::active()->orderBy('nama_supplier')->get();

        return view('manajer.procurements.create', compact('items', 'suppliers'));
    }

    public function store(ProcurementRequest $request)
    {
        $data = $request->validated();

        $totalHarga = $data['qty'] * $data['harga_satuan'];

        $procurement = Procurement::create([
            'no_pengadaan'       => $this->docNumber->forProcurement(),
            'item_id'            => $data['item_id'],
            'supplier_id'        => $data['supplier_id'],
            'user_id'            => auth()->id(),
            'qty'                => $data['qty'],
            'harga_satuan'       => $data['harga_satuan'],
            'total_harga'        => $totalHarga,
            'tanggal'            => $data['tanggal'],
            'tanggal_dibutuhkan' => $data['tanggal_dibutuhkan'] ?? null,
            'status'             => 'pending',
            'catatan'            => $data['catatan'] ?? null,
        ]);

        // Notifikasi ke admin bahwa ada pengadaan baru
        $this->notification->sendNewProcurementNotification($procurement);

        return redirect()->route('manajer.procurements.index')
            ->with('success', "Pengadaan {$procurement->no_pengadaan} berhasil diajukan.");
    }

    public function show(Procurement $procurement)
    {
        $procurement->load(['item', 'supplier', 'user', 'approvedBy', 'stockIn']);
        return view('manajer.procurements.show', compact('procurement'));
    }

    public function edit(Procurement $procurement)
    {
        if (!in_array($procurement->status, ['draft', 'pending'])) {
            return back()->with('error', 'Pengadaan tidak dapat diedit pada status ini.');
        }

        $items     = Item::active()->orderBy('nama_barang')->get();
        $suppliers = Supplier::active()->orderBy('nama_supplier')->get();

        return view('manajer.procurements.edit', compact('procurement', 'items', 'suppliers'));
    }

    public function update(ProcurementRequest $request, Procurement $procurement)
    {
        if (!in_array($procurement->status, ['draft', 'pending'])) {
            return back()->with('error', 'Pengadaan tidak dapat diperbarui.');
        }

        $data = $request->validated();
        $procurement->update($data);

        return redirect()->route('manajer.procurements.index')
            ->with('success', 'Pengadaan berhasil diperbarui.');
    }

    public function destroy(Procurement $procurement)
    {
        if ($procurement->status !== 'draft') {
            return back()->with('error', 'Hanya pengadaan dengan status draft yang dapat dihapus.');
        }

        $procurement->delete();

        return redirect()->route('manajer.procurements.index')
            ->with('success', 'Pengadaan berhasil dihapus.');
    }

    public function submit(Procurement $procurement)
    {
        if ($procurement->status !== 'draft') {
            return back()->with('error', 'Hanya draft yang bisa diajukan.');
        }

        $procurement->update(['status' => 'pending']);
        $this->notification->sendNewProcurementNotification($procurement);

        return back()->with('success', 'Pengadaan berhasil diajukan untuk persetujuan.');
    }

    public function approve(Request $request, Procurement $procurement)
    {
        if ($procurement->status !== 'pending') {
            return back()->with('error', 'Pengadaan sudah diproses sebelumnya.');
        }

        $procurement->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'catatan'     => $request->catatan ?? $procurement->catatan,
        ]);

        $this->notification->sendProcurementApprovedNotification($procurement);

        return back()->with('success', "Pengadaan {$procurement->no_pengadaan} disetujui.");
    }

    public function reject(Request $request, Procurement $procurement)
    {
        $request->validate(['catatan' => 'required|string|max:500']);

        if ($procurement->status !== 'pending') {
            return back()->with('error', 'Pengadaan sudah diproses sebelumnya.');
        }

        $procurement->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'catatan'     => $request->catatan,
        ]);

        $this->notification->sendProcurementRejectedNotification($procurement);

        return back()->with('success', "Pengadaan {$procurement->no_pengadaan} ditolak.");
    }
}