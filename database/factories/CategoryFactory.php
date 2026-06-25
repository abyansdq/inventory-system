<?php
// database/factories/CategoryFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $categories = [
            'Bahan Baku Utama',
            'Bahan Baku Pendukung',
            'Bahan Kemasan',
            'Spare Part',
            'Alat Produksi',
            'Bahan Kimia',
            'Bahan Elektrik',
        ];

        $nama = $this->faker->unique()->randomElement($categories);

        return [
            'nama_kategori' => $nama,
            'kode_kategori' => 'KAT-' . strtoupper($this->faker->unique()->lexify('???')),
            'deskripsi'     => $this->faker->sentence(),
            'is_active'     => true,
        ];
    }
}