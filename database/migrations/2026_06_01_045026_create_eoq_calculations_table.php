<?php
// database/migrations/xxxx_create_eoq_calculations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eoq_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->cascadeOnDelete();
            $table->foreignId('calculated_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Input Parameter
            $table->decimal('demand_tahunan', 15, 2);       // D - unit/tahun
            $table->decimal('ordering_cost', 15, 2);        // S - biaya pesan
            $table->decimal('holding_cost', 15, 2);         // H - biaya simpan/unit/tahun

            // Output EOQ
            $table->decimal('eoq_result', 15, 2);           // Hasil EOQ

            // Safety Stock
            $table->decimal('demand_harian_avg', 15, 4);    // d rata-rata
            $table->decimal('demand_harian_max', 15, 4);    // d maksimum
            $table->integer('lead_time');                    // L (hari)
            $table->decimal('safety_stock', 15, 2);         // Safety stock result

            // Reorder Point
            $table->decimal('rop_result', 15, 2);           // ROP = (d × L) + Safety Stock

            // Frekuensi Pemesanan
            $table->decimal('frekuensi_pesan', 10, 2);      // D / EOQ
            $table->decimal('interval_pesan', 10, 2);       // 365 / frekuensi (hari)

            $table->timestamp('tanggal_hitung');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eoq_calculations');
    }
};