<?php
// app/Services/StockService.php

namespace App\Services;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\ItemRequest;
use App\Models\Procurement;
use App\Exceptions\InsufficientStockException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    public function __construct(
        private DocumentNumberService $documentNumber,
        private NotificationService   $notification
    ) {}

    // =========================================================
    // BARANG MASUK
    // =========================================================

    /**
     * Proses barang masuk ke gudang.
     *
     * @param  array  $data
     * @param  int    $userId  User yang menginput
     * @return StockIn
     */
    public function processStockIn(array $data, int $userId): StockIn
    {
        return DB::transaction(function () use ($data, $userId) {

            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            // Hitung total harga
            $totalHarga = ($data['qty'] * $data['harga_satuan']);

            // Buat record stock_in
            $stockIn = StockIn::create([
                'no_dokumen'     => $this->documentNumber->forStockIn(),
                'item_id'        => $data['item_id'],
                'supplier_id'    => $data['supplier_id'],
                'procurement_id' => $data['procurement_id'] ?? null,
                'user_id'        => $userId,
                'qty'            => $data['qty'],
                'harga_satuan'   => $data['harga_satuan'],
                'total_harga'    => $totalHarga,
                'tanggal'        => $data['tanggal'],
                'keterangan'     => $data['keterangan'] ?? null,
            ]);

            // Update stok barang
            $stokLama = $item->stok;
            $item->increment('stok', $data['qty']);
            $item->refresh();

            Log::info("StockIn: Item [{$item->kode_barang}] {$item->nama_barang} "
                . "stok {$stokLama} → {$item->stok}");

            // Update status procurement jika ada
            if (!empty($data['procurement_id'])) {
                $this->updateProcurementStatus(
                    $data['procurement_id'],
                    'received'
                );
            }

            // Kirim notifikasi jika stok masih menipis meski sudah ditambah
            if ($item->stok <= $item->stok_minimum) {
                $this->notification->sendLowStockNotification($item);
            }

            return $stockIn;
        });
    }

    // =========================================================
    // BARANG KELUAR
    // =========================================================

    /**
     * Proses barang keluar dari gudang.
     *
     * @param  array  $data
     * @param  int    $userId
     * @return StockOut
     *
     * @throws InsufficientStockException
     */
    public function processStockOut(array $data, int $userId): StockOut
    {
        return DB::transaction(function () use ($data, $userId) {

            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            // Validasi stok mencukupi
            $this->validateStock($item, $data['qty']);

            // Buat record stock_out
            $stockOut = StockOut::create([
                'no_dokumen'      => $this->documentNumber->forStockOut(),
                'item_id'         => $data['item_id'],
                'item_request_id' => $data['item_request_id'] ?? null,
                'user_id'         => $userId,
                'qty'             => $data['qty'],
                'tanggal'         => $data['tanggal'],
                'keterangan'      => $data['keterangan'] ?? null,
            ]);

            // Update stok barang
            $stokLama = $item->stok;
            $item->decrement('stok', $data['qty']);
            $item->refresh();

            Log::info("StockOut: Item [{$item->kode_barang}] {$item->nama_barang} "
                . "stok {$stokLama} → {$item->stok}");

            // Update status item_request jika ada
            if (!empty($data['item_request_id'])) {
                $this->updateItemRequestStatus(
                    $data['item_request_id'],
                    'processed'
                );
            }

            // Cek kondisi stok setelah pengurangan
            $this->checkStockCondition($item);

            return $stockOut;
        });
    }

    // =========================================================
    // PROSES PERMINTAAN BARANG (Item Request)
    // =========================================================

    /**
     * Approve permintaan barang dan langsung proses barang keluar.
     *
     * @throws InsufficientStockException
     */
    public function approveAndProcessRequest(
        ItemRequest $itemRequest,
        int         $adminId,
        ?string     $catatan = null
    ): StockOut {
        return DB::transaction(function () use ($itemRequest, $adminId, $catatan) {

            $item = Item::lockForUpdate()->findOrFail($itemRequest->item_id);

            // Validasi stok
            $this->validateStock($item, $itemRequest->qty);

            // Update status permintaan → approved
            $itemRequest->update([
                'status'       => 'approved',
                'approved_by'  => $adminId,
                'approved_at'  => now(),
                'catatan_admin'=> $catatan,
            ]);

            // Proses barang keluar
            $stockOut = $this->processStockOut([
                'item_id'         => $itemRequest->item_id,
                'item_request_id' => $itemRequest->id,
                'qty'             => $itemRequest->qty,
                'tanggal'         => today(),
                'keterangan'      => "Proses dari permintaan {$itemRequest->no_permintaan}",
            ], $adminId);

            // Kirim notifikasi ke user peminta
            $this->notification->sendRequestApprovedNotification($itemRequest);

            return $stockOut;
        });
    }

    /**
     * Tolak permintaan barang.
     */
    public function rejectRequest(
        ItemRequest $itemRequest,
        int         $adminId,
        string      $catatan
    ): ItemRequest {
        $itemRequest->update([
            'status'        => 'rejected',
            'approved_by'   => $adminId,
            'approved_at'   => now(),
            'catatan_admin' => $catatan,
        ]);

        // Kirim notifikasi ke user
        $this->notification->sendRequestRejectedNotification($itemRequest);

        return $itemRequest;
    }

    // =========================================================
    // STOK KOREKSI (Penyesuaian Manual)
    // =========================================================

    /**
     * Koreksi stok secara manual oleh admin.
     * Bisa menambah atau mengurangi tergantung selisih.
     */
    public function adjustStock(
        Item   $item,
        int    $stokBaru,
        string $keterangan,
        int    $userId
    ): void {
        DB::transaction(function () use ($item, $stokBaru, $keterangan, $userId) {

            $selisih  = $stokBaru - $item->stok;
            $tanggal  = today();

            if ($selisih > 0) {
                // Stok bertambah → catat sebagai stock_in
                StockIn::create([
                    'no_dokumen'   => $this->documentNumber->forStockIn(),
                    'item_id'      => $item->id,
                    'supplier_id'  => $item->supplier_id,
                    'user_id'      => $userId,
                    'qty'          => $selisih,
                    'harga_satuan' => 0,
                    'total_harga'  => 0,
                    'tanggal'      => $tanggal,
                    'keterangan'   => "[KOREKSI] {$keterangan}",
                ]);
            } elseif ($selisih < 0) {
                // Stok berkurang → catat sebagai stock_out
                StockOut::create([
                    'no_dokumen' => $this->documentNumber->forStockOut(),
                    'item_id'    => $item->id,
                    'user_id'    => $userId,
                    'qty'        => abs($selisih),
                    'tanggal'    => $tanggal,
                    'keterangan' => "[KOREKSI] {$keterangan}",
                ]);
            }

            // Update stok langsung
            $item->update(['stok' => $stokBaru]);

            Log::info("StockAdjust: Item [{$item->kode_barang}] "
                . "stok dikoreksi → {$stokBaru}. Oleh user_id: {$userId}");

            // Cek kondisi stok setelah koreksi
            $this->checkStockCondition($item->fresh());
        });
    }

    // =========================================================
    // PROCUREMENT
    // =========================================================

    /**
     * Update status procurement.
     */
    public function updateProcurementStatus(int $procurementId, string $status): void
    {
        Procurement::where('id', $procurementId)->update(['status' => $status]);
    }

    /**
     * Update status item request.
     */
    private function updateItemRequestStatus(int $requestId, string $status): void
    {
        ItemRequest::where('id', $requestId)->update(['status' => $status]);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Validasi stok mencukupi.
     *
     * @throws InsufficientStockException
     */
    private function validateStock(Item $item, int $qty): void
    {
        if ($item->stok < $qty) {
            throw new InsufficientStockException(
                $item->nama_barang,
                $qty,
                $item->stok
            );
        }
    }

    /**
     * Cek kondisi stok dan kirim notifikasi yang sesuai.
     */
    private function checkStockCondition(Item $item): void
    {
        if ($item->stok <= 0) {
            // Stok habis
            $this->notification->sendOutOfStockNotification($item);
        } elseif ($item->stok <= $item->safety_stock) {
            // Di bawah safety stock — perlu reorder segera
            $this->notification->sendReorderPointNotification($item);
        } elseif ($item->stok <= $item->stok_minimum) {
            // Di bawah stok minimum
            $this->notification->sendLowStockNotification($item);
        }
    }

    // =========================================================
    // STATISTICS / SUMMARY
    // =========================================================

    /**
     * Ringkasan pergerakan stok untuk suatu barang.
     */
    public function getStockSummary(Item $item): array
    {
        $totalMasuk  = StockIn::where('item_id', $item->id)->sum('qty');
        $totalKeluar = StockOut::where('item_id', $item->id)->sum('qty');

        $masukBulanIni  = StockIn::where('item_id', $item->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('qty');

        $keluarBulanIni = StockOut::where('item_id', $item->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('qty');

        return [
            'stok_saat_ini'     => $item->stok,
            'stok_minimum'      => $item->stok_minimum,
            'safety_stock'      => $item->safety_stock,
            'status_stok'       => $item->status_stok,
            'total_masuk'       => $totalMasuk,
            'total_keluar'      => $totalKeluar,
            'masuk_bulan_ini'   => $masukBulanIni,
            'keluar_bulan_ini'  => $keluarBulanIni,
            'nilai_stok'        => $item->stok * $item->harga_beli,
        ];
    }

    /**
     * Data grafik pergerakan stok per hari (N hari terakhir).
     */
    public function getMovementChart(Item $item, int $days = 30): array
    {
        $labels   = [];
        $masuk    = [];
        $keluar   = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('d/m');

            $masuk[]  = StockIn::where('item_id', $item->id)
                ->whereDate('tanggal', $date)->sum('qty') ?? 0;

            $keluar[] = StockOut::where('item_id', $item->id)
                ->whereDate('tanggal', $date)->sum('qty') ?? 0;
        }

        return [
            'labels' => $labels,
            'masuk'  => $masuk,
            'keluar' => $keluar,
        ];
    }
}