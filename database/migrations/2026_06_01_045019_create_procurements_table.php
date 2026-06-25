<?php
// database/migrations/xxxx_create_procurements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengadaan', 50)->unique();
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->restrictOnDelete();
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->restrictOnDelete();
            $table->foreignId('user_id')            // Yang mengajukan
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->integer('qty');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->date('tanggal');
            $table->date('tanggal_dibutuhkan')->nullable();
            $table->enum('status', [
                'draft',
                'pending',
                'approved',
                'rejected',
                'ordered',      // Sudah dipesan ke supplier
                'received',     // Sudah diterima
                'cancelled'
            ])->default('draft');
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan')->nullable();
            $table->string('no_dokumen_referensi', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};