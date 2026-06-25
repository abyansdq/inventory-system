<?php
// app/Services/ForecastService.php

namespace App\Services;

use App\Models\Item;
use App\Models\Forecast;
use App\Models\DemandHistory;
use App\Exceptions\InsufficientDataException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForecastService
{
    // Nama bulan dalam Bahasa Indonesia
    private array $namaBulan = [
        1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
        4  => 'April',    5  => 'Mei',      6  => 'Juni',
        7  => 'Juli',     8  => 'Agustus',  9  => 'September',
        10 => 'Oktober',  11 => 'November', 12 => 'Desember',
    ];

    // =========================================================
    // WEIGHTED MOVING AVERAGE (WMA)
    // =========================================================

    /**
     * Generate prediksi menggunakan Weighted Moving Average.
     *
     * Bobot default untuk n=3: [1, 2, 3] (yang terbaru bobot terbesar)
     * Normalisasi: total bobot = 1
     *
     * Rumus:
     * WMA = Σ(bobot[i] × data[i]) / Σ(bobot[i])
     *
     * @param  Item         $item
     * @param  int          $n          Jumlah periode (default: 3)
     * @param  int          $userId
     * @param  array|null   $bobot      Custom bobot (opsional)
     * @return Forecast
     *
     * @throws InsufficientDataException
     */
    public function generate(
        Item  $item,
        int   $n      = 3,
        int   $userId = 0,
        ?array $bobot = null
    ): Forecast {
        return DB::transaction(function () use ($item, $n, $userId, $bobot) {

            // Ambil histori demand terurut
            $histories = $this->getHistories($item, $n);

            // Validasi jumlah data
            if ($histories->count() < $n) {
                throw new InsufficientDataException(
                    'prediksi WMA',
                    $n,
                    $histories->count()
                );
            }

            // Ambil N data terakhir
            $dataN = $histories->take($n);

            // Generate atau pakai bobot custom
            $bobot = $bobot ?? $this->generateBobot($n);

            // Validasi bobot
            $this->validateBobot($bobot, $n);

            // Hitung WMA
            $hasilPrediksi = $this->calculateWMA(
                $dataN->pluck('jumlah_permintaan')->toArray(),
                $bobot
            );

            // Tentukan periode prediksi (bulan berikutnya)
            $periodePrediksi = $this->getNextPeriode($histories->first());

            // Simpan ke database (update jika sudah ada)
            $forecast = Forecast::updateOrCreate(
                [
                    'item_id'        => $item->id,
                    'tahun_prediksi' => $periodePrediksi['tahun'],
                    'bulan_prediksi' => $periodePrediksi['bulan'],
                    'metode'         => 'weighted_moving_average',
                ],
                [
                    'generated_by'   => $userId,
                    'periode_bulan'  => $n,
                    'hasil_prediksi' => $hasilPrediksi,
                    'bobot'          => $bobot,
                    'keterangan'     => "WMA {$n} periode: "
                                      . implode(', ', $bobot),
                ]
            );

            Log::info("Forecast WMA: Item [{$item->kode_barang}] "
                . "prediksi {$periodePrediksi['label']} = {$hasilPrediksi}");

            return $forecast;
        });
    }

    /**
     * Generate prediksi untuk beberapa bulan ke depan sekaligus.
     *
     * @param  Item   $item
     * @param  int    $bulanKedepan  Jumlah bulan prediksi
     * @param  int    $n             Periode WMA
     * @param  int    $userId
     * @return array  Collection of Forecast
     */
    public function generateMultiple(
        Item $item,
        int  $bulanKedepan = 3,
        int  $n            = 3,
        int  $userId       = 0
    ): array {
        $results  = [];
        $bobot    = $this->generateBobot($n);

        // Ambil semua histori awal
        $histories = $this->getHistories($item, null);

        if ($histories->count() < $n) {
            throw new InsufficientDataException(
                'prediksi multiple WMA',
                $n,
                $histories->count()
            );
        }

        // Convert ke array untuk manipulasi
        $demandData = $histories
            ->pluck('jumlah_permintaan', 'bulan')
            ->toArray();

        $allData    = $histories->map(fn($h) => [
            'tahun'  => $h->tahun,
            'bulan'  => $h->bulan,
            'demand' => $h->jumlah_permintaan,
        ])->toArray();

        for ($i = 0; $i < $bulanKedepan; $i++) {

            // Ambil N data terakhir dari allData
            $dataN    = array_slice($allData, -$n);
            $demands  = array_column($dataN, 'demand');

            // Hitung prediksi
            $predicted = $this->calculateWMA($demands, $bobot);

            // Tentukan periode berikutnya
            $lastData  = end($allData);
            $nextPeriod = $this->getNextPeriodeFromYearMonth(
                $lastData['tahun'],
                $lastData['bulan']
            );

            // Simpan forecast
            $forecast = Forecast::updateOrCreate(
                [
                    'item_id'        => $item->id,
                    'tahun_prediksi' => $nextPeriod['tahun'],
                    'bulan_prediksi' => $nextPeriod['bulan'],
                    'metode'         => 'weighted_moving_average',
                ],
                [
                    'generated_by'   => $userId,
                    'periode_bulan'  => $n,
                    'hasil_prediksi' => $predicted,
                    'bobot'          => $bobot,
                    'keterangan'     => "WMA {$n} periode (prediksi ke-" . ($i + 1) . ")",
                ]
            );

            $results[] = $forecast;

            // Tambahkan hasil prediksi ke allData untuk prediksi berikutnya
            $allData[] = [
                'tahun'  => $nextPeriod['tahun'],
                'bulan'  => $nextPeriod['bulan'],
                'demand' => $predicted,
            ];
        }

        return $results;
    }

    // =========================================================
    // FORMULA INTI WMA
    // =========================================================

    /**
     * Hitung Weighted Moving Average.
     *
     * Contoh n=3, data=[100, 120, 110], bobot=[1, 2, 3]:
     * WMA = (100×1 + 120×2 + 110×3) / (1+2+3)
     *     = (100 + 240 + 330) / 6
     *     = 670 / 6
     *     = 111.67
     *
     * @param  array  $data   Data permintaan (urut dari lama ke baru)
     * @param  array  $bobot  Bobot per periode (jumlah sama dengan data)
     * @return float
     */
    public function calculateWMA(array $data, array $bobot): float
    {
        $n            = count($data);
        $totalBobot   = array_sum($bobot);
        $weightedSum  = 0;

        for ($i = 0; $i < $n; $i++) {
            $weightedSum += $data[$i] * $bobot[$i];
        }

        if ($totalBobot <= 0) return 0;

        return round($weightedSum / $totalBobot, 2);
    }

    /**
     * Generate bobot default untuk n periode.
     * Bobot: 1, 2, 3, ..., n (yang terbaru bobotnya paling besar)
     *
     * Contoh n=3: [1, 2, 3]
     * Contoh n=4: [1, 2, 3, 4]
     */
    public function generateBobot(int $n): array
    {
        return range(1, $n);
    }

    // =========================================================
    // AKURASI / ERROR METRICS
    // =========================================================

    /**
     * Hitung akurasi prediksi menggunakan MAE dan MAPE.
     *
     * Dipanggil setelah periode aktual tersedia.
     *
     * MAE  = (1/n) × Σ|Actual - Forecast|
     * MAPE = (1/n) × Σ|(Actual - Forecast) / Actual| × 100%
     */
    public function calculateAccuracy(Item $item): array
    {
        // Ambil forecast yang sudah ada aktual-nya
        $forecasts = Forecast::where('item_id', $item->id)
            ->whereNotNull('actual_demand')
            ->where('metode', 'weighted_moving_average')
            ->get();

        if ($forecasts->isEmpty()) {
            return [
                'mae'           => null,
                'mape'          => null,
                'jumlah_data'   => 0,
                'keterangan'    => 'Belum ada data aktual untuk evaluasi.',
            ];
        }

        $maeSum  = 0;
        $mapeSum = 0;
        $n       = $forecasts->count();

        foreach ($forecasts as $forecast) {
            $error   = abs($forecast->actual_demand - $forecast->hasil_prediksi);
            $maeSum  += $error;

            if ($forecast->actual_demand > 0) {
                $mapeSum += ($error / $forecast->actual_demand) * 100;
            }
        }

        $mae  = $maeSum  / $n;
        $mape = $mapeSum / $n;

        // Update error ke masing-masing forecast
        foreach ($forecasts as $forecast) {
            $error = abs($forecast->actual_demand - $forecast->hasil_prediksi);
            $forecast->update([
                'error_mae'  => $error,
                'error_mape' => $forecast->actual_demand > 0
                    ? ($error / $forecast->actual_demand) * 100
                    : null,
            ]);
        }

        return [
            'mae'           => round($mae, 4),
            'mape'          => round($mape, 4),
            'jumlah_data'   => $n,
            'interpretasi'  => $this->interpretasiMape($mape),
        ];
    }

    /**
     * Update nilai aktual pada forecast tertentu.
     */
    public function updateActual(Forecast $forecast, float $actualDemand): Forecast
    {
        $error = abs($actualDemand - $forecast->hasil_prediksi);
        $mape  = $actualDemand > 0
            ? ($error / $actualDemand) * 100
            : null;

        $forecast->update([
            'actual_demand' => $actualDemand,
            'error_mae'     => $error,
            'error_mape'    => $mape,
        ]);

        return $forecast;
    }

    // =========================================================
    // HELPERS & UTILITIES
    // =========================================================

    /**
     * Ambil histori demand terurut dari yang paling baru.
     */
    private function getHistories(Item $item, ?int $limit): Collection
    {
        $query = DemandHistory::where('item_id', $item->id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        // Kembalikan urutan dari lama ke baru
        return $query->get()->reverse()->values();
    }

    /**
     * Tentukan periode prediksi berikutnya.
     */
    private function getNextPeriode(DemandHistory $lastHistory): array
    {
        return $this->getNextPeriodeFromYearMonth(
            $lastHistory->tahun,
            $lastHistory->bulan
        );
    }

    /**
     * Hitung periode bulan berikutnya dari tahun & bulan.
     */
    public function getNextPeriodeFromYearMonth(int $tahun, int $bulan): array
    {
        if ($bulan == 12) {
            $nextBulan = 1;
            $nextTahun = $tahun + 1;
        } else {
            $nextBulan = $bulan + 1;
            $nextTahun = $tahun;
        }

        return [
            'tahun' => $nextTahun,
            'bulan' => $nextBulan,
            'label' => ($this->namaBulan[$nextBulan] ?? $nextBulan) . ' ' . $nextTahun,
        ];
    }

    /**
     * Validasi bobot.
     */
    private function validateBobot(array $bobot, int $n): void
    {
        if (count($bobot) !== $n) {
            throw new \InvalidArgumentException(
                "Jumlah bobot ({$n}) harus sama dengan jumlah periode."
            );
        }

        foreach ($bobot as $b) {
            if ($b < 0) {
                throw new \InvalidArgumentException(
                    'Bobot tidak boleh bernilai negatif.'
                );
            }
        }

        if (array_sum($bobot) <= 0) {
            throw new \InvalidArgumentException(
                'Total bobot harus lebih dari 0.'
            );
        }
    }

    /**
     * Interpretasi nilai MAPE.
     */
    private function interpretasiMape(float $mape): string
    {
        return match(true) {
            $mape < 10  => 'Sangat Akurat (MAPE < 10%)',
            $mape < 20  => 'Akurat (MAPE 10-20%)',
            $mape < 50  => 'Cukup Akurat (MAPE 20-50%)',
            default     => 'Kurang Akurat (MAPE > 50%)',
        };
    }

    /**
     * Ambil semua data untuk tampilan chart forecast.
     */
    public function getChartData(Item $item): array
    {
        // Data historis
        $histories = DemandHistory::where('item_id', $item->id)
            ->orderByPeriode()
            ->get();

        // Data forecast
        $forecasts = Forecast::where('item_id', $item->id)
            ->where('metode', 'weighted_moving_average')
            ->orderBy('tahun_prediksi')
            ->orderBy('bulan_prediksi')
            ->get();

        $labels      = [];
        $aktual      = [];
        $prediksi    = [];

        // Data historis
        foreach ($histories as $h) {
            $labels[]   = ($this->namaBulan[$h->bulan] ?? $h->bulan) . ' ' . $h->tahun;
            $aktual[]   = $h->jumlah_permintaan;
            $prediksi[] = null;
        }

        // Data prediksi (tidak ada aktual)
        foreach ($forecasts as $f) {
            $label = ($this->namaBulan[$f->bulan_prediksi] ?? $f->bulan_prediksi)
                   . ' ' . $f->tahun_prediksi;

            // Hindari duplikat label
            if (!in_array($label, $labels)) {
                $labels[]   = $label;
                $aktual[]   = $f->actual_demand;    // null jika belum ada
                $prediksi[] = $f->hasil_prediksi;
            }
        }

        return [
            'labels'   => $labels,
            'aktual'   => $aktual,
            'prediksi' => $prediksi,
        ];
    }
}