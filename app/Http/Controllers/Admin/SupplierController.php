<?php
// app/Http/Controllers/Admin/SupplierController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Supplier;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount(['items', 'stockIns', 'procurements']);

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('kode_supplier', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create(DocumentNumberService $docNumber)
    {
        $kodeDefault = 'SUP-' . str_pad(
            Supplier::withTrashed()->count() + 1,
            4, '0', STR_PAD_LEFT
        );
        return view('admin.suppliers.create', compact('kodeDefault'));
    }

    public function store(SupplierRequest $request)
    {
        Supplier::create($request->validated());

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['items']);
        $stockIns     = $supplier->stockIns()->with('item')->latest()->paginate(10);
        $procurements = $supplier->procurements()->with('item')->latest()->limit(5)->get();

        return view('admin.suppliers.show', compact('supplier', 'stockIns', 'procurements'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->items()->exists()) {
            return back()->with('error',
                'Supplier tidak dapat dihapus karena masih terkait dengan barang.'
            );
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}