<?php
// database/migrations/xxxx_create_stock_ins_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->string('no_dokumen', 50)->unique();
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->restrictOnDelete();
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->restrictOnDelete();
            $table->foreignId('procurement_id')
                  ->nullable()
                  ->constrained('procurements')
                  ->nullOnDelete();
            $table->foreignId('user_id')            // Yang menginput
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->integer('qty');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};