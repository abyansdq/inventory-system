<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =====================
        // DEFINE ALL PERMISSIONS
        // =====================
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Master - Barang
            'items.view',
            'items.create',
            'items.edit',
            'items.delete',

            // Master - Kategori
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',

            // Master - Supplier
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',

            // Master - User
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Transaksi - Barang Masuk
            'stock-ins.view',
            'stock-ins.create',
            'stock-ins.edit',
            'stock-ins.delete',

            // Transaksi - Barang Keluar
            'stock-outs.view',
            'stock-outs.create',
            'stock-outs.edit',
            'stock-outs.delete',

            // Transaksi - Permintaan Barang
            'item-requests.view',
            'item-requests.create',
            'item-requests.approve',
            'item-requests.reject',

            // Transaksi - Pengadaan
            'procurements.view',
            'procurements.create',
            'procurements.approve',
            'procurements.reject',

            // Analisis
            'eoq.view',
            'eoq.calculate',
            'forecasts.view',
            'forecasts.generate',

            // Monitoring
            'monitoring.view',

            // Laporan
            'reports.view',
            'reports.export',
        ];

        // Buat semua permission
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // =====================
        // BUAT ROLES
        // =====================

        // --- ROLE: Admin Gudang ---
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions([
            'dashboard.view',
            'items.view', 'items.create', 'items.edit', 'items.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'stock-ins.view', 'stock-ins.create', 'stock-ins.edit', 'stock-ins.delete',
            'stock-outs.view', 'stock-outs.create', 'stock-outs.edit', 'stock-outs.delete',
            'item-requests.view', 'item-requests.approve', 'item-requests.reject',
            'procurements.view',
            'eoq.view', 'eoq.calculate',
            'forecasts.view', 'forecasts.generate',
            'monitoring.view',
            'reports.view', 'reports.export',
        ]);

        // --- ROLE: Manajer ---
        $managerRole = Role::firstOrCreate(['name' => 'manajer', 'guard_name' => 'web']);
        $managerRole->syncPermissions([
            'dashboard.view',
            'items.view',
            'suppliers.view',
            'stock-ins.view',
            'stock-outs.view',
            'item-requests.view',
            'procurements.view', 'procurements.create', 'procurements.approve', 'procurements.reject',
            'eoq.view',
            'forecasts.view',
            'monitoring.view',
            'reports.view', 'reports.export',
        ]);

        // --- ROLE: User ---
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions([
            'dashboard.view',
            'items.view',
            'item-requests.view', 'item-requests.create',
            'forecasts.view',
            'monitoring.view',
        ]);

        $this->command->info('✅ Roles dan Permissions berhasil dibuat.');
    }
}