<?php
// app/Notifications/LowStockNotification.php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Item   $item,
        private string $type = 'low_stock'  // 'low_stock' | 'out_of_stock'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isOutOfStock = $this->type === 'out_of_stock';

        return [
            'type'      => $this->type,
            'item_id'   => $this->item->id,
            'title'     => $isOutOfStock
                ? "⛔ Stok Habis: {$this->item->nama_barang}"
                : "⚠️ Stok Menipis: {$this->item->nama_barang}",
            'message'   => $isOutOfStock
                ? "Stok {$this->item->nama_barang} telah habis. Segera lakukan pengadaan."
                : "Stok {$this->item->nama_barang} tinggal {$this->item->stok} {$this->item->satuan} "
                  . "(minimum: {$this->item->stok_minimum} {$this->item->satuan}).",
            'url'       => route('admin.items.show', $this->item->id),
            'stok'      => $this->item->stok,
            'minimum'   => $this->item->stok_minimum,
        ];
    }
}