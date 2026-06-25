<?php
// app/Console/Commands/CheckLowStock.php

namespace App\Console\Commands;

use App\Models\Item;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature   = 'inventory:check-stock';
    protected $description = 'Cek stok menipis dan kirim notifikasi';

    public function __construct(private NotificationService $notification)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $lowStockItems = Item::active()->lowStock()->get();

        if ($lowStockItems->isEmpty()) {
            $this->info('✅ Semua stok dalam kondisi aman.');
            return;
        }

        $this->warn("⚠️ Ditemukan {$lowStockItems->count()} barang stok menipis:");

        foreach ($lowStockItems as $item) {
            $this->line("  - {$item->nama_barang}: {$item->stok}/{$item->stok_minimum}");
            $this->notification->sendLowStockNotification($item);
        }

        $this->info('Notifikasi telah dikirim ke Admin dan Manajer.');
    }
}