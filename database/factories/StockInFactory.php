<?php
// database/factories/StockInFactory.php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockInFactory extends Factory
{
    public function definition(): array
    {
        $qty          = $this->faker->numberBetween(10, 200);
        $harga_satuan = $this->faker->numberBetween(10000, 500000);

        return [
            'no_dokumen'    => 'SIN-' . date('Ymd') . '-' . strtoupper($this->faker->unique()->lexify('????')),
            'item_id'       => Item::inRandomOrder()->first()?->id ?? Item::factory(),
            'supplier_id'   => Supplier::inRandomOrder()->first()?->id ?? Supplier::factory(),
            'procurement_id'=> null,
            'user_id'       => User::role('admin')->first()?->id,
            'qty'           => $qty,
            'harga_satuan'  => $harga_satuan,
            'total_harga'   => $qty * $harga_satuan,
            'tanggal'       => $this->faker->dateTimeBetween('-6 months', 'now'),
            'keterangan'    => $this->faker->optional()->sentence(),
        ];
    }
}