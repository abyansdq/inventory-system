<?php
// database/factories/StockOutFactory.php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockOutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'no_dokumen'      => 'SOUT-' . date('Ymd') . '-' . strtoupper($this->faker->unique()->lexify('????')),
            'item_id'         => Item::inRandomOrder()->first()?->id ?? Item::factory(),
            'item_request_id' => null,
            'user_id'         => User::role('admin')->first()?->id,
            'qty'             => $this->faker->numberBetween(1, 50),
            'tanggal'         => $this->faker->dateTimeBetween('-6 months', 'now'),
            'keterangan'      => $this->faker->optional()->sentence(),
        ];
    }
}