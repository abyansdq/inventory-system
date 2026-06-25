<?php
// database/migrations/xxxx_create_demand_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demand_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->cascadeOnDelete();
            $table->integer('tahun');
            $table->integer('bulan');               // 1-12
            $table->integer('jumlah_permintaan');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Unique: satu barang hanya punya satu record per bulan per tahun
            $table->unique(['item_id', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demand_histories');
    }
};