<?php
// database/factories/ProcurementFactory.php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcurementFactory extends Factory
{
    public function definition(): array
    {
        $qty          = $this->faker->numberBetween(50, 500);
        $harga_satuan = $this->faker->numberBetween(10000, 500000);
        $status       = $this->faker->randomElement(['draft', 'pending', 'approved', 'received']);

        return [
            'no_pengadaan'          => 'PRC-' . date('Ymd') . '-' . strtoupper($this->faker->unique()->lexify('????')),
            'item_id'               => Item::inRandomOrder()->first()?->id ?? Item::factory(),
            'supplier_id'           => Supplier::inRandomOrder()->first()?->id ?? Supplier::factory(),
            'user_id'               => User::role('manajer')->first()?->id,
            'qty'                   => $qty,
            'harga_satuan'          => $harga_satuan,
            'total_harga'           => $qty * $harga_satuan,
            'tanggal'               => $this->faker->dateTimeBetween('-6 months', 'now'),
            'tanggal_dibutuhkan'    => $this->faker->dateTimeBetween('now', '+2 months'),
            'status'                => $status,
            'approved_by'           => in_array($status, ['approved', 'received'])
                                        ? User::role('manajer')->first()?->id
                                        : null,
            'approved_at'           => in_array($status, ['approved', 'received'])
                                        ? now()
                                        : null,
            'catatan'               => $this->faker->optional()->sentence(),
        ];
    }
}