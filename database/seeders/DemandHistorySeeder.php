<?php
// database/seeders/DemandHistorySeeder.php

namespace Database\Seeders;

use App\Models\DemandHistory;
use App\Models\Item;
use Illuminate\Database\Seeder;

class DemandHistorySeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::all();

        // Generate histori 12 bulan terakhir untuk setiap item
        foreach ($items as $item) {
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                // Simulasi permintaan dengan variasi realistis
                $base   = rand(100, 300);
                $variasi = rand(-30, 50);

                DemandHistory::firstOrCreate(
                    [
                        'item_id' => $item->id,
                        'tahun'   => 2024,
                        'bulan'   => $bulan,
                    ],
                    [
                        'jumlah_permintaan' => max(10, $base + $variasi),
                        'keterangan'        => null,
                    ]
                );
            }
        }

        $this->command->info('✅ Demand history 12 bulan berhasil dibuat untuk semua item.');
    }
}