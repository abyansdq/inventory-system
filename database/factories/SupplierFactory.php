<?php
// database/factories/SupplierFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode_supplier'  => 'SUP-' . strtoupper($this->faker->unique()->lexify('????')),
            'nama_supplier'  => 'CV/PT ' . $this->faker->company(),
            'contact_person' => $this->faker->name(),
            'email'          => $this->faker->companyEmail(),
            'telepon'        => '08' . $this->faker->numerify('##########'),
            'alamat'         => $this->faker->streetAddress(),
            'kota'           => $this->faker->city(),
            'keterangan'     => $this->faker->optional()->sentence(),
            'is_active'      => true,
        ];
    }
}