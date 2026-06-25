<?php
// database/migrations/xxxx_create_forecasts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->cascadeOnDelete();
            $table->foreignId('generated_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->enum('metode', [
                'moving_average',
                'weighted_moving_average',
                'linear_regression'
            ])->default('weighted_moving_average');

            $table->integer('periode_bulan');       // Periode n bulan yang digunakan
            $table->integer('tahun_prediksi');
            $table->integer('bulan_prediksi');      // 1-12
            $table->decimal('hasil_prediksi', 15, 2);

            // Data akurasi (diisi setelah periode berjalan)
            $table->decimal('actual_demand', 15, 2)->nullable();
            $table->decimal('error_mae', 15, 4)->nullable();    // Mean Absolute Error
            $table->decimal('error_mape', 10, 4)->nullable();   // Mean Absolute Percentage Error

            // Bobot WMA (disimpan sebagai JSON)
            $table->json('bobot')->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['item_id', 'tahun_prediksi', 'bulan_prediksi', 'metode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};