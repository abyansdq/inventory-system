<?php
// app/Notifications/ReorderPointNotification.php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReorderPointNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Item $item) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'      => 'reorder_point',
            'item_id'   => $this->item->id,
            'title'     => "🔄 Reorder Point: {$this->item->nama_barang}",
            'message'   => "Stok {$this->item->nama_barang} ({$this->item->stok} {$this->item->satuan}) "
                         . "telah mencapai Reorder Point. Segera buat pengadaan.",
            'url'       => route('admin.eoq.show', $this->item->id),
            'stok'      => $this->item->stok,
            'safety_stock' => $this->item->safety_stock,
        ];
    }
}