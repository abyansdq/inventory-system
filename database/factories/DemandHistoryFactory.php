<?php
// database/factories/DemandHistoryFactory.php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class DemandHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id'            => Item::inRandomOrder()->first()?->id ?? Item::factory(),
            'tahun'              => $this->faker->numberBetween(2022, 2024),
            'bulan'              => $this->faker->numberBetween(1, 12),
            'jumlah_permintaan'  => $this->faker->numberBetween(50, 300),
            'keterangan'         => null,
        ];
    }
}