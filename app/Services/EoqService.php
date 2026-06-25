<?php
// app/Services/EoqService.php

namespace App\Services;

use App\Models\Item;
use App\Models\EoqCalculation;
use App\Models\DemandHistory;
use App\Exceptions\InsufficientDataException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EoqService
{
    // Jumlah hari kerja dalam setahun
    private const DAYS_PER_YEAR = 365;

    // Nilai Z untuk service level 95% (standar)
    private const Z_VALUE = 1.645;

    // =========================================================
    // KALKULASI EOQ UTAMA
    // =========================================================

    /**
     * Hitung EOQ lengkap beserta Safety Stock dan ROP.
     * Simpan hasil ke database.
     *
     * @param  Item   $item
     * @param  array  $params  Override parameter (opsional)
     * @param  int    $userId
     * @return EoqCalculation
     *
     * @throws InsufficientDataException
     */
    public function calculate(Item $item, array $params = [], int $userId = 0): EoqCalculation
    {
        return DB::transaction(function () use ($item, $params, $userId) {

            // Ambil demand dari histori atau override
            $demandData = $this->getDemandData($item);

            // Parameter kalkulasi (bisa di-override dari input form)
            $demandTahunan  = $params['demand_tahunan']  ?? $demandData['demand_tahunan'];
            $orderingCost   = $params['ordering_cost']   ?? $item->ordering_cost;
            $holdingCost    = $params['holding_cost']    ?? $item->holding_cost;
            $leadTime       = $params['lead_time']       ?? $item->lead_time;

            // Validasi parameter tidak boleh 0
            $this->validateParameters($demandTahunan, $orderingCost, $holdingCost, $leadTime);

            // ---- Hitung EOQ ----
            $eoqResult = $this->calculateEOQ($demandTahunan, $orderingCost, $holdingCost);

            // ---- Hitung Safety Stock ----
            $safetyStock = $this->calculateSafetyStock(
                $demandData['demand_harian_max'],
                $demandData['demand_harian_avg'],
                $leadTime
            );

            // ---- Hitung ROP ----
            $rop = $this->calculateROP(
                $demandData['demand_harian_avg'],
                $leadTime,
                $safetyStock
            );

            // ---- Hitung Frekuensi & Interval Pesan ----
            $frekuensiPesan = $demandTahunan > 0 ? $demandTahunan / $eoqResult : 0;
            $intervalPesan  = $frekuensiPesan > 0 ? self::DAYS_PER_YEAR / $frekuensiPesan : 0;

            // Simpan ke database
            $eoqCalc = EoqCalculation::create([
                'item_id'           => $item->id,
                'calculated_by'     => $userId,
                'demand_tahunan'    => $demandTahunan,
                'ordering_cost'     => $orderingCost,
                'holding_cost'      => $holdingCost,
                'eoq_result'        => $eoqResult,
                'demand_harian_avg' => $demandData['demand_harian_avg'],
                'demand_harian_max' => $demandData['demand_harian_max'],
                'lead_time'         => $leadTime,
                'safety_stock'      => $safetyStock,
                'rop_result'        => $rop,
                'frekuensi_pesan'   => $frekuensiPesan,
                'interval_pesan'    => $intervalPesan,
                'tanggal_hitung'    => now(),
                'keterangan'        => $params['keterangan'] ?? null,
            ]);

            // Sync safety_stock & rop ke tabel items
            $item->update([
                'safety_stock' => round($safetyStock),
                'stok_minimum' => round($rop),
            ]);

            Log::info("EOQ Calculated: Item [{$item->kode_barang}] "
                . "EOQ={$eoqResult}, SS={$safetyStock}, ROP={$rop}");

            return $eoqCalc;
        });
    }

    // =========================================================
    // FORMULA INTI
    // =========================================================

    /**
     * Rumus EOQ:
     * EOQ = sqrt((2 × D × S) / H)
     *
     * D = Demand tahunan (unit/tahun)
     * S = Ordering cost (biaya sekali pesan)
     * H = Holding cost (biaya simpan per unit per tahun)
     */
    public function calculateEOQ(
        float $demandTahunan,
        float $orderingCost,
        float $holdingCost
    ): float {
        if ($holdingCost <= 0) return 0;

        $eoq = sqrt((2 * $demandTahunan * $orderingCost) / $holdingCost);

        return round($eoq, 2);
    }

    /**
     * Rumus Safety Stock (Simplified):
     * SS = (Max Demand - Avg Demand) × Lead Time
     *
     * Digunakan rumus yang sederhana dan mudah dijelaskan di skripsi.
     */
    public function calculateSafetyStock(
        float $maxDemandHarian,
        float $avgDemandHarian,
        int   $leadTime
    ): float {
        $safetyStock = ($maxDemandHarian - $avgDemandHarian) * $leadTime;

        return round(max(0, $safetyStock), 2);
    }

    /**
     * Rumus Reorder Point:
     * ROP = (d × L) + Safety Stock
     *
     * d = rata-rata permintaan harian
     * L = lead time (hari)
     */
    public function calculateROP(
        float $avgDemandHarian,
        int   $leadTime,
        float $safetyStock
    ): float {
        $rop = ($avgDemandHarian * $leadTime) + $safetyStock;

        return round(max(0, $rop), 2);
    }

    // =========================================================
    // DATA DEMAND
    // =========================================================

    /**
     * Ambil dan olah data demand dari histori.
     *
     * @throws InsufficientDataException
     */
    public function getDemandData(Item $item): array
    {
        $histories = DemandHistory::where('item_id', $item->id)
            ->orderByPeriode()
            ->get();

        if ($histories->count() < 3) {
            throw new InsufficientDataException(
                'kalkulasi EOQ',
                3,
                $histories->count()
            );
        }

        $demands = $histories->pluck('jumlah_permintaan')->toArray();

        // Demand tahunan = rata-rata per bulan × 12
        $avgBulanan   = array_sum($demands) / count($demands);
        $demandTahunan = $avgBulanan * 12;

        // Demand harian
        $avgHarian    = $demandTahunan / self::DAYS_PER_YEAR;
        $maxBulanan   = max($demands);
        $maxHarian    = $maxBulanan / 30;   // Estimasi max harian

        return [
            'demand_tahunan'    => round($demandTahunan, 2),
            'demand_harian_avg' => round($avgHarian, 4),
            'demand_harian_max' => round($maxHarian, 4),
            'demand_bulanan_avg'=> round($avgBulanan, 2),
            'jumlah_data'       => count($demands),
            'histories'         => $histories,
        ];
    }

    // =========================================================
    // HASIL RINGKAS (untuk ditampilkan ke view)
    // =========================================================

    /**
     * Ambil hasil EOQ terbaru untuk suatu item.
     */
    public function getLatestResult(Item $item): ?EoqCalculation
    {
        return EoqCalculation::where('item_id', $item->id)
            ->latest('tanggal_hitung')
            ->first();
    }

    /**
     * Generate summary lengkap untuk tampilan detail.
     */
    public function getSummary(EoqCalculation $calc): array
    {
        return [
            // Input
            'demand_tahunan'    => $calc->demand_tahunan,
            'ordering_cost'     => $calc->ordering_cost,
            'holding_cost'      => $calc->holding_cost,
            'lead_time'         => $calc->lead_time,

            // Output Utama
            'eoq'               => $calc->eoq_result,
            'safety_stock'      => $calc->safety_stock,
            'rop'               => $calc->rop_result,

            // Turunan
            'frekuensi_pesan'   => $calc->frekuensi_pesan,
            'interval_pesan'    => $calc->interval_pesan,

            // Biaya
            'total_biaya_pesan' => $calc->frekuensi_pesan * $calc->ordering_cost,
            'total_biaya_simpan'=> ($calc->eoq_result / 2) * $calc->holding_cost,
            'total_biaya'       => ($calc->frekuensi_pesan * $calc->ordering_cost)
                                 + (($calc->eoq_result / 2) * $calc->holding_cost),

            // Interpretasi
            'interpretasi'      => $this->interpretasi($calc),
        ];
    }

    /**
     * Buat kalimat interpretasi hasil EOQ.
     */
    public function interpretasi(EoqCalculation $calc): string
    {
        $eoq        = round($calc->eoq_result);
        $rop        = round($calc->rop_result);
        $ss         = round($calc->safety_stock);
        $interval   = round($calc->interval_pesan);
        $itemName   = $calc->item->nama_barang;
        $satuan     = $calc->item->satuan;

        return "Untuk barang <strong>{$itemName}</strong>, jumlah pemesanan optimal adalah "
            . "<strong>{$eoq} {$satuan}</strong> setiap kali pesan. "
            . "Pemesanan ulang dilakukan ketika stok mencapai <strong>{$rop} {$satuan}</strong> (ROP). "
            . "Safety stock yang disarankan adalah <strong>{$ss} {$satuan}</strong>. "
            . "Dengan interval pemesanan sekitar <strong>{$interval} hari</strong> sekali.";
    }

    // =========================================================
    // VALIDASI
    // =========================================================

    private function validateParameters(
        float $demand,
        float $orderingCost,
        float $holdingCost,
        int   $leadTime
    ): void {
        $errors = [];

        if ($demand <= 0)       $errors[] = 'Demand tahunan harus lebih dari 0.';
        if ($orderingCost <= 0) $errors[] = 'Biaya pemesanan harus lebih dari 0.';
        if ($holdingCost <= 0)  $errors[] = 'Biaya penyimpanan harus lebih dari 0.';
        if ($leadTime <= 0)     $errors[] = 'Lead time harus lebih dari 0.';

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }
    }
}