<?php
// database/factories/ItemRequestFactory.php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemRequestFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'approved', 'rejected', 'processed']);

        return [
            'no_permintaan' => 'REQ-' . date('Ymd') . '-' . strtoupper($this->faker->unique()->lexify('????')),
            'user_id'       => User::role('user')->inRandomOrder()->first()?->id,
            'item_id'       => Item::inRandomOrder()->first()?->id ?? Item::factory(),
            'qty'           => $this->faker->numberBetween(1, 50),
            'tanggal'       => $this->faker->dateTimeBetween('-6 months', 'now'),
            'tanggal_butuh' => $this->faker->dateTimeBetween('now', '+1 month'),
            'keperluan'     => $this->faker->sentence(),
            'status'        => $status,
            'approved_by'   => in_array($status, ['approved', 'rejected', 'processed'])
                                ? User::role('admin')->first()?->id
                                : null,
            'approved_at'   => in_array($status, ['approved', 'rejected', 'processed'])
                                ? now()
                                : null,
            'catatan_admin' => $this->faker->optional()->sentence(),
        ];
    }
}