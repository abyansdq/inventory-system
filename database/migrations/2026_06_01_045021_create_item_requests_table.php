<?php
// database/migrations/xxxx_create_item_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->string('no_permintaan', 50)->unique();
            $table->foreignId('user_id')            // Yang mengajukan
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->restrictOnDelete();
            $table->integer('qty');
            $table->date('tanggal');
            $table->date('tanggal_butuh')->nullable();  // Kapan barang dibutuhkan
            $table->text('keperluan')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'processed',    // Sudah diproses (barang keluar)
                'cancelled'
            ])->default('pending');
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_requests');
    }
};