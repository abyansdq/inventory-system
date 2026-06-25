<?php
// app/Services/CacheService.php

namespace App\Services;

use App\Models\Item;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    // TTL dalam detik
    private const TTL_SHORT  = 300;    // 5 menit
    private const TTL_MEDIUM = 1800;   // 30 menit
    private const TTL_LONG   = 86400;  // 24 jam

    // -------------------------------------------------------
    // Dashboard Stats — cache 5 menit
    // -------------------------------------------------------
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard.stats', self::TTL_SHORT, function () {
            return [
                'total_barang'   => Item::active()->count(),
                'stok_menipis'   => Item::active()->lowStock()->count(),
                'stok_habis'     => Item::active()->where('stok', 0)->count(),
                'total_supplier' => Supplier::active()->count(),
                'total_kategori' => Category::active()->count(),
            ];
        });
    }

    // -------------------------------------------------------
    // Dropdown data — cache 30 menit (jarang berubah)
    // -------------------------------------------------------
    public function getActiveItems(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('items.active', self::TTL_MEDIUM, function () {
            return Item::active()->orderBy('nama_barang')->get(['id', 'nama_barang', 'satuan', 'stok', 'supplier_id']);
        });
    }

    public function getActiveCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('categories.active', self::TTL_MEDIUM, function () {
            return Category::active()->orderBy('nama_kategori')->get();
        });
    }

    public function getActiveSuppliers(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('suppliers.active', self::TTL_MEDIUM, function () {
            return Supplier::active()->orderBy('nama_supplier')->get();
        });
    }

    // -------------------------------------------------------
    // Clear cache setelah perubahan data
    // -------------------------------------------------------
    public function clearItemCache(): void
    {
        Cache::forget('items.active');
        Cache::forget('dashboard.stats');
    }

    public function clearCategoryCache(): void
    {
        Cache::forget('categories.active');
    }

    public function clearSupplierCache(): void
    {
        Cache::forget('suppliers.active');
    }

    public function clearAllCache(): void
    {
        Cache::flush();
    }
}