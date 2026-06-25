<?php
// database/seeders/MasterDataSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 5 Kategori
        $categories = [
            ['nama_kategori' => 'Bahan Baku Utama',      'kode_kategori' => 'KAT-001', 'deskripsi' => 'Bahan baku utama produksi'],
            ['nama_kategori' => 'Bahan Baku Pendukung',  'kode_kategori' => 'KAT-002', 'deskripsi' => 'Bahan baku pendukung produksi'],
            ['nama_kategori' => 'Bahan Kemasan',         'kode_kategori' => 'KAT-003', 'deskripsi' => 'Material untuk kemasan produk'],
            ['nama_kategori' => 'Spare Part',            'kode_kategori' => 'KAT-004', 'deskripsi' => 'Suku cadang mesin produksi'],
            ['nama_kategori' => 'Bahan Kimia',           'kode_kategori' => 'KAT-005', 'deskripsi' => 'Bahan kimia industri'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['kode_kategori' => $cat['kode_kategori']],
                array_merge($cat, ['is_active' => true])
            );
        }

        // Buat 5 Supplier
        $suppliers = [
            [
                'kode_supplier'  => 'SUP-0001',
                'nama_supplier'  => 'PT Sumber Makmur',
                'contact_person' => 'Budi Santoso',
                'email'          => 'budi@sumbermakmur.com',
                'telepon'        => '0812-3456-7890',
                'alamat'         => 'Jl. Industri No. 15',
                'kota'           => 'Jakarta',
            ],
            [
                'kode_supplier'  => 'SUP-0002',
                'nama_supplier'  => 'CV Maju Bersama',
                'contact_person' => 'Siti Rahayu',
                'email'          => 'siti@majubersama.com',
                'telepon'        => '0823-4567-8901',
                'alamat'         => 'Jl. Raya Industri No. 28',
                'kota'           => 'Surabaya',
            ],
            [
                'kode_supplier'  => 'SUP-0003',
                'nama_supplier'  => 'PT Global Supply',
                'contact_person' => 'Ahmad Fauzi',
                'email'          => 'ahmad@globalsupply.com',
                'telepon'        => '0834-5678-9012',
                'alamat'         => 'Jl. Sudirman No. 100',
                'kota'           => 'Bandung',
            ],
            [
                'kode_supplier'  => 'SUP-0004',
                'nama_supplier'  => 'UD Karya Mandiri',
                'contact_person' => 'Dewi Lestari',
                'email'          => 'dewi@karyamandiri.com',
                'telepon'        => '0845-6789-0123',
                'alamat'         => 'Jl. Gatot Subroto No. 55',
                'kota'           => 'Semarang',
            ],
            [
                'kode_supplier'  => 'SUP-0005',
                'nama_supplier'  => 'PT Prima Jaya',
                'contact_person' => 'Eko Prasetyo',
                'email'          => 'eko@primajaya.com',
                'telepon'        => '0856-7890-1234',
                'alamat'         => 'Jl. Diponegoro No. 77',
                'kota'           => 'Yogyakarta',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(
                ['kode_supplier' => $sup['kode_supplier']],
                array_merge($sup, ['is_active' => true])
            );
        }

        // Buat 10 Item
        $catIds = Category::pluck('id')->toArray();
        $supIds = Supplier::pluck('id')->toArray();

        $items = [
            [
                'kode_barang'   => 'BRG-00001',
                'nama_barang'   => 'Tepung Terigu',
                'category_id'   => $catIds[0],
                'supplier_id'   => $supIds[0],
                'satuan'        => 'kg',
                'stok'          => 500,
                'stok_minimum'  => 100,
                'safety_stock'  => 50,
                'harga_beli'    => 12000,
                'harga_jual'    => 14000,
                'ordering_cost' => 150000,
                'holding_cost'  => 500,
                'lead_time'     => 3,
            ],
            [
                'kode_barang'   => 'BRG-00002',
                'nama_barang'   => 'Gula Pasir',
                'category_id'   => $catIds[0],
                'supplier_id'   => $supIds[1],
                'satuan'        => 'kg',
                'stok'          => 300,
                'stok_minimum'  => 80,
                'safety_stock'  => 40,
                'harga_beli'    => 14000,
                'harga_jual'    => 16000,
                'ordering_cost' => 120000,
                'holding_cost'  => 600,
                'lead_time'     => 2,
            ],
            [
                'kode_barang'   => 'BRG-00003',
                'nama_barang'   => 'Minyak Goreng',
                'category_id'   => $catIds[0],
                'supplier_id'   => $supIds[2],
                'satuan'        => 'liter',
                'stok'          => 200,
                'stok_minimum'  => 50,
                'safety_stock'  => 25,
                'harga_beli'    => 18000,
                'harga_jual'    => 21000,
                'ordering_cost' => 100000,
                'holding_cost'  => 800,
                'lead_time'     => 4,
            ],
            [
                'kode_barang'   => 'BRG-00004',
                'nama_barang'   => 'Kardus Kemasan A4',
                'category_id'   => $catIds[2],
                'supplier_id'   => $supIds[3],
                'satuan'        => 'pcs',
                'stok'          => 1000,
                'stok_minimum'  => 200,
                'safety_stock'  => 100,
                'harga_beli'    => 3500,
                'harga_jual'    => 4500,
                'ordering_cost' => 80000,
                'holding_cost'  => 200,
                'lead_time'     => 5,
            ],
            [
                'kode_barang'   => 'BRG-00005',
                'nama_barang'   => 'Plastik Wrap',
                'category_id'   => $catIds[2],
                'supplier_id'   => $supIds[4],
                'satuan'        => 'roll',
                'stok'          => 150,
                'stok_minimum'  => 30,
                'safety_stock'  => 15,
                'harga_beli'    => 45000,
                'harga_jual'    => 55000,
                'ordering_cost' => 90000,
                'holding_cost'  => 1500,
                'lead_time'     => 3,
            ],
            [
                'kode_barang'   => 'BRG-00006',
                'nama_barang'   => 'Bearing SKF 6205',
                'category_id'   => $catIds[3],
                'supplier_id'   => $supIds[0],
                'satuan'        => 'pcs',
                'stok'          => 80,
                'stok_minimum'  => 20,
                'safety_stock'  => 10,
                'harga_beli'    => 85000,
                'harga_jual'    => 100000,
                'ordering_cost' => 120000,
                'holding_cost'  => 3000,
                'lead_time'     => 7,
            ],
            [
                'kode_barang'   => 'BRG-00007',
                'nama_barang'   => 'Oli Mesin SAE 40',
                'category_id'   => $catIds[1],
                'supplier_id'   => $supIds[1],
                'satuan'        => 'liter',
                'stok'          => 120,
                'stok_minimum'  => 30,
                'safety_stock'  => 15,
                'harga_beli'    => 35000,
                'harga_jual'    => 42000,
                'ordering_cost' => 100000,
                'holding_cost'  => 1200,
                'lead_time'     => 4,
            ],
            [
                'kode_barang'   => 'BRG-00008',
                'nama_barang'   => 'Cairan Pembersih Industri',
                'category_id'   => $catIds[4],
                'supplier_id'   => $supIds[2],
                'satuan'        => 'liter',
                'stok'          => 60,
                'stok_minimum'  => 20,
                'safety_stock'  => 10,
                'harga_beli'    => 55000,
                'harga_jual'    => 65000,
                'ordering_cost' => 85000,
                'holding_cost'  => 2000,
                'lead_time'     => 5,
            ],
            [
                'kode_barang'   => 'BRG-00009',
                'nama_barang'   => 'Label Stiker Produk',
                'category_id'   => $catIds[2],
                'supplier_id'   => $supIds[3],
                'satuan'        => 'lembar',
                'stok'          => 5000,
                'stok_minimum'  => 1000,
                'safety_stock'  => 500,
                'harga_beli'    => 500,
                'harga_jual'    => 700,
                'ordering_cost' => 60000,
                'holding_cost'  => 50,
                'lead_time'     => 2,
            ],
            [
                'kode_barang'   => 'BRG-00010',
                'nama_barang'   => 'Baut M10 Stainless',
                'category_id'   => $catIds[3],
                'supplier_id'   => $supIds[4],
                'satuan'        => 'pcs',
                'stok'          => 2000,
                'stok_minimum'  => 500,
                'safety_stock'  => 200,
                'harga_beli'    => 2500,
                'harga_jual'    => 3500,
                'ordering_cost' => 70000,
                'holding_cost'  => 100,
                'lead_time'     => 3,
            ],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(
                ['kode_barang' => $item['kode_barang']],
                array_merge($item, ['is_active' => true])
            );
        }

        $this->command->info('✅ Master data (categories, suppliers, items) berhasil dibuat.');
    }
}