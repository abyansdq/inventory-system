<?php
// database/migrations/xxxx_create_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 200);
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->restrictOnDelete();
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->restrictOnDelete();
            $table->string('satuan', 50);             // pcs, kg, liter, dll
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->integer('safety_stock')->default(0);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);

            // Parameter EOQ
            $table->decimal('ordering_cost', 15, 2)->default(0);   // Biaya pesan (S)
            $table->decimal('holding_cost', 15, 2)->default(0);    // Biaya simpan/unit/tahun (H)
            $table->integer('lead_time')->default(0);               // Hari

            // Info tambahan
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};