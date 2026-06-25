<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use App\Models\ItemRequest;
use App\Models\Procurement;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;
use App\Notifications\ReorderPointNotification;
use App\Notifications\OutOfStockNotification;
use App\Notifications\ItemRequestNotification;
use App\Notifications\ProcurementNotification;

class NotificationService
{
    // =========================================================
    // STOK NOTIFICATIONS
    // =========================================================

    /**
     * Notifikasi stok menipis (di bawah stok minimum).
     */
    public function sendLowStockNotification(Item $item): void
    {
        $recipients = $this->getAdminAndManager();

        foreach ($recipients as $user) {
            $user->notify(new LowStockNotification($item, 'low_stock'));
        }
    }

    /**
     * Notifikasi stok habis.
     */
    public function sendOutOfStockNotification(Item $item): void
    {
        $recipients = $this->getAdminAndManager();

        foreach ($recipients as $user) {
            $user->notify(new LowStockNotification($item, 'out_of_stock'));
        }
    }

    /**
     * Notifikasi sudah mencapai Reorder Point.
     */
    public function sendReorderPointNotification(Item $item): void
    {
        $recipients = $this->getAdminAndManager();

        foreach ($recipients as $user) {
            $user->notify(new ReorderPointNotification($item));
        }
    }

    // =========================================================
    // ITEM REQUEST NOTIFICATIONS
    // =========================================================

    /**
     * Notifikasi ada permintaan baru (ke Admin).
     */
    public function sendNewRequestNotification(ItemRequest $itemRequest): void
    {
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new ItemRequestNotification($itemRequest, 'new_request'));
        }
    }

    /**
     * Notifikasi permintaan disetujui (ke User peminta).
     */
    public function sendRequestApprovedNotification(ItemRequest $itemRequest): void
    {
        $itemRequest->user->notify(
            new ItemRequestNotification($itemRequest, 'approved')
        );
    }

    /**
     * Notifikasi permintaan ditolak (ke User peminta).
     */
    public function sendRequestRejectedNotification(ItemRequest $itemRequest): void
    {
        $itemRequest->user->notify(
            new ItemRequestNotification($itemRequest, 'rejected')
        );
    }

    // =========================================================
    // PROCUREMENT NOTIFICATIONS
    // =========================================================

    /**
     * Notifikasi pengadaan baru (ke Manajer).
     */
    public function sendNewProcurementNotification(Procurement $procurement): void
    {
        $managers = User::role('manajer')->get();

        foreach ($managers as $manager) {
            $manager->notify(new ProcurementNotification($procurement, 'new_procurement'));
        }
    }

    /**
     * Notifikasi pengadaan disetujui (ke Admin Gudang).
     */
    public function sendProcurementApprovedNotification(Procurement $procurement): void
    {
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new ProcurementNotification($procurement, 'approved'));
        }
    }

    /**
     * Notifikasi pengadaan ditolak (ke yang mengajukan).
     */
    public function sendProcurementRejectedNotification(Procurement $procurement): void
    {
        $procurement->user->notify(
            new ProcurementNotification($procurement, 'rejected')
        );
    }

    // =========================================================
    // HELPER
    // =========================================================

    /**
     * Ambil semua Admin dan Manajer.
     */
    private function getAdminAndManager()
    {
        return User::role(['admin', 'manajer'])->get();
    }
}