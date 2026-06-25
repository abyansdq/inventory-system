<?php
// app/Http/Controllers/Admin/ItemController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ItemController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = Item::with(['category', 'supplier']);

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status_stok')) {
            match($request->status_stok) {
                'menipis' => $query->lowStock(),
                'habis'   => $query->where('stok', 0),
                'aman'    => $query->where('stok', '>', 0)->whereColumn('stok', '>', 'stok_minimum'),
                default   => null,
            };
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $items      = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::active()->get();

        return view('admin.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $suppliers  = Supplier::active()->get();
        $kodeDefault = 'BRG-' . str_pad(
            Item::withTrashed()->count() + 1,
            5, '0', STR_PAD_LEFT
        );

        return view('admin.items.create', compact('categories', 'suppliers', 'kodeDefault'));
    }

    public function store(ItemRequest $request)
    {
        $data = $request->validated();

        // Handle upload foto
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->uploadFoto($request->file('foto'));
        }

        Item::create($data);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'supplier']);
        $summary       = $this->stockService->getStockSummary($item);
        $chartData     = $this->stockService->getMovementChart($item, 30);
        $latestStockIn = $item->stockIns()->with('supplier', 'user')->latest()->limit(5)->get();
        $latestStockOut = $item->stockOuts()->with('user')->latest()->limit(5)->get();

        return view('admin.items.show', compact(
            'item', 'summary', 'chartData',
            'latestStockIn', 'latestStockOut'
        ));
    }

    public function edit(Item $item)
    {
        $categories = Category::active()->get();
        $suppliers  = Supplier::active()->get();
        return view('admin.items.edit', compact('item', 'categories', 'suppliers'));
    }

    public function update(ItemRequest $request, Item $item)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($item->foto) {
                Storage::disk('public')->delete($item->foto);
            }
            $data['foto'] = $this->uploadFoto($request->file('foto'));
        }

        $item->update($data);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->stockIns()->exists() || $item->stockOuts()->exists()) {
            return back()->with('error',
                'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi.'
            );
        }

        if ($item->foto) {
            Storage::disk('public')->delete($item->foto);
        }

        $item->delete();

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    private function uploadFoto($file): string
    {
        $filename = 'items/' . uniqid() . '.' . $file->getClientOriginalExtension();

        $image = Image::read($file)
            ->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

        Storage::disk('public')->put($filename, $image->encode());

        return $filename;
    }
}