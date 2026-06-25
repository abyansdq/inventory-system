<?php
// app/Notifications/ItemRequestNotification.php

namespace App\Notifications;

use App\Models\ItemRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ItemRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private ItemRequest $itemRequest,
        private string      $type   // 'new_request' | 'approved' | 'rejected'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $item     = $this->itemRequest->item;
        $user     = $this->itemRequest->user;
        $noReq    = $this->itemRequest->no_permintaan;

        return match($this->type) {
            'new_request' => [
                'type'    => 'new_request',
                'title'   => "📋 Permintaan Baru: {$noReq}",
                'message' => "{$user->name} mengajukan permintaan {$this->itemRequest->qty} "
                           . "{$item->satuan} {$item->nama_barang}.",
                'url'     => route('admin.item-requests.show', $this->itemRequest->id),
            ],
            'approved' => [
                'type'    => 'approved',
                'title'   => "✅ Permintaan Disetujui: {$noReq}",
                'message' => "Permintaan Anda untuk {$item->nama_barang} "
                           . "({$this->itemRequest->qty} {$item->satuan}) telah disetujui.",
                'url'     => route('user.item-requests.show', $this->itemRequest->id),
            ],
            'rejected' => [
                'type'    => 'rejected',
                'title'   => "❌ Permintaan Ditolak: {$noReq}",
                'message' => "Permintaan Anda untuk {$item->nama_barang} ditolak. "
                           . "Alasan: {$this->itemRequest->catatan_admin}",
                'url'     => route('user.item-requests.show', $this->itemRequest->id),
            ],
            default => [],
        };
    }
}