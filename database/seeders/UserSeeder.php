<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Gudang
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'      => 'Admin Gudang',
                'password'  => Hash::make('password'),
                'phone'     => '08123456789',
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // Manajer
        $manajer = User::firstOrCreate(
            ['email' => 'manajer@gmail.com'],
            [
                'name'      => 'Manajer',
                'password'  => Hash::make('password'),
                'phone'     => '08234567890',
                'is_active' => true,
            ]
        );
        $manajer->assignRole('manajer');

        // User
        $user = User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name'      => 'User Gudang',
                'password'  => Hash::make('password'),
                'phone'     => '08345678901',
                'is_active' => true,
            ]
        );
        $user->assignRole('user');

        $this->command->info('✅ Users berhasil dibuat.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin Gudang', 'admin@gmail.com', 'password'],
                ['Manajer', 'manajer@gmail.com', 'password'],
                ['User', 'user@gmail.com', 'password'],
            ]
        );
    }
}