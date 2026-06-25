<?php
// app/Services/DocumentNumberService.php

namespace App\Services;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\ItemRequest;
use App\Models\Procurement;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Prefix mapping per dokumen.
     */
    private array $prefixes = [
        'stock_in'    => 'BM',   // Barang Masuk
        'stock_out'   => 'BK',   // Barang Keluar
        'item_request'=> 'PR',   // Permintaan
        'procurement' => 'PRC',  // Pengadaan
    ];

    /**
     * Generate nomor dokumen baru.
     *
     * Format: PREFIX/YYYY/MM/XXXX
     * Contoh: BM/2024/12/0001
     */
    public function generate(string $type): string
    {
        $prefix = $this->prefixes[$type] ?? strtoupper($type);
        $year   = now()->format('Y');
        $month  = now()->format('m');
        $latest = $this->getLatestNumber($type, $year, $month);

        $sequence = str_pad($latest + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}/{$year}/{$month}/{$sequence}";
    }

    /**
     * Ambil nomor urut terakhir untuk bulan & tahun ini.
     */
    private function getLatestNumber(string $type, string $year, string $month): int
    {
        $pattern = $this->getPattern($type, $year, $month);
        $column  = 'no_dokumen';

        $model = match($type) {
            'stock_in'     => StockIn::class,
            'stock_out'    => StockOut::class,
            'item_request' => ItemRequest::class,
            'procurement'  => Procurement::class,
            default        => null,
        };

        if (!$model) return 0;

        // Untuk item_request dan procurement pakai kolom yang berbeda
        if ($type === 'item_request') $column = 'no_permintaan';
        if ($type === 'procurement')  $column = 'no_pengadaan';

        $latest = $model::withTrashed()
            ->where($column, 'like', $pattern)
            ->orderBy($column, 'desc')
            ->value($column);

        if (!$latest) return 0;

        // Ambil 4 digit terakhir (sequence)
        $parts = explode('/', $latest);
        return (int) end($parts);
    }

    /**
     * Pattern untuk LIKE query.
     */
    private function getPattern(string $type, string $year, string $month): string
    {
        $prefix = $this->prefixes[$type] ?? strtoupper($type);
        return "{$prefix}/{$year}/{$month}/%";
    }

    /**
     * Generate semua nomor dokumen — shortcut methods.
     */
    public function forStockIn(): string
    {
        return $this->generate('stock_in');
    }

    public function forStockOut(): string
    {
        return $this->generate('stock_out');
    }

    public function forItemRequest(): string
    {
        return $this->generate('item_request');
    }

    public function forProcurement(): string
    {
        return $this->generate('procurement');
    }

    /**
     * Validasi apakah nomor dokumen sudah ada (duplicate check).
     */
    public function isUnique(string $type, string $number): bool
    {
        $column = 'no_dokumen';
        $model  = match($type) {
            'stock_in'     => StockIn::class,
            'stock_out'    => StockOut::class,
            'item_request' => ItemRequest::class,
            'procurement'  => Procurement::class,
            default        => null,
        };

        if (!$model) return true;

        if ($type === 'item_request') $column = 'no_permintaan';
        if ($type === 'procurement')  $column = 'no_pengadaan';

        return !$model::withTrashed()->where($column, $number)->exists();
    }
}