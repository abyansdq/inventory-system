<?php
// database/migrations/xxxx_create_stock_outs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->string('no_dokumen', 50)->unique();
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->restrictOnDelete();
            $table->foreignId('item_request_id')
                  ->nullable()
                  ->constrained('item_requests')
                  ->nullOnDelete();
            $table->foreignId('user_id')            // Yang menginput
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->integer('qty');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};