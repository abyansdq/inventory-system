<?php
// database/factories/ItemFactory.php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        $satuan = $this->faker->randomElement(['pcs', 'kg', 'liter', 'meter', 'box', 'roll', 'unit']);

        return [
            'kode_barang'   => 'BRG-' . strtoupper($this->faker->unique()->lexify('?????')),
            'nama_barang'   => $this->faker->words(3, true),
            'category_id'   => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'supplier_id'   => Supplier::inRandomOrder()->first()?->id ?? Supplier::factory(),
            'satuan'        => $satuan,
            'stok'          => $this->faker->numberBetween(50, 500),
            'stok_minimum'  => $this->faker->numberBetween(10, 50),
            'safety_stock'  => $this->faker->numberBetween(5, 30),
            'harga_beli'    => $this->faker->numberBetween(10000, 500000),
            'harga_jual'    => $this->faker->numberBetween(15000, 600000),
            'ordering_cost' => $this->faker->numberBetween(50000, 200000),
            'holding_cost'  => $this->faker->numberBetween(1000, 10000),
            'lead_time'     => $this->faker->numberBetween(1, 14),
            'foto'          => null,
            'deskripsi'     => $this->faker->optional()->sentence(),
            'is_active'     => true,
        ];
    }
}