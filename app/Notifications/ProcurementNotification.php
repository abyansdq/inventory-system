<?php
// app/Notifications/ProcurementNotification.php

namespace App\Notifications;

use App\Models\Procurement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProcurementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Procurement $procurement,
        private string      $type
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $item = $this->procurement->item;
        $no   = $this->procurement->no_pengadaan;

        return match($this->type) {
            'new_procurement' => [
                'type'    => 'procurement',
                'title'   => "🛒 Pengadaan Baru: {$no}",
                'message' => "Pengajuan pengadaan {$item->nama_barang} "
                           . "sejumlah {$this->procurement->qty} {$item->satuan} menunggu persetujuan.",
                'url'     => route('manajer.procurements.show', $this->procurement->id),
            ],
            'approved' => [
                'type'    => 'procurement',
                'title'   => "✅ Pengadaan Disetujui: {$no}",
                'message' => "Pengadaan {$item->nama_barang} telah disetujui. "
                           . "Siapkan penerimaan barang.",
                'url'     => route('admin.procurements.show', $this->procurement->id),
            ],
            'rejected' => [
                'type'    => 'procurement',
                'title'   => "❌ Pengadaan Ditolak: {$no}",
                'message' => "Pengadaan {$item->nama_barang} ditolak. "
                           . "Catatan: {$this->procurement->catatan}",
                'url'     => route('manajer.procurements.show', $this->procurement->id),
            ],
            default => [],
        };
    }
}