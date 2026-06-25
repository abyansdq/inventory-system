<?php
// database/migrations/xxxx_add_indexes_to_all_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Items — kolom yang sering di-query
        Schema::table('items', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('supplier_id');
            $table->index('is_active');
            $table->index('stok');
            $table->index(['stok', 'stok_minimum']);   // Untuk scope lowStock
        });

        // Stock Ins — filter tanggal & barang
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->index('item_id');
            $table->index('supplier_id');
            $table->index('tanggal');
            $table->index('user_id');
            $table->index(['item_id', 'tanggal']);
        });

        // Stock Outs — filter tanggal & barang
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->index('item_id');
            $table->index('tanggal');
            $table->index('user_id');
            $table->index(['item_id', 'tanggal']);
        });

        // Item Requests — filter status & user
        Schema::table('item_requests', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('item_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
        });

        // Procurements — filter status & item
        Schema::table('procurements', function (Blueprint $table) {
            $table->index('item_id');
            $table->index('supplier_id');
            $table->index('status');
            $table->index('user_id');
        });

        // Demand Histories — query per item & periode
        Schema::table('demand_histories', function (Blueprint $table) {
            $table->index('item_id');
            $table->index(['item_id', 'tahun', 'bulan']);
        });

        // EOQ Calculations
        Schema::table('eoq_calculations', function (Blueprint $table) {
            $table->index('item_id');
            $table->index('tanggal_hitung');
        });

        // Forecasts
        Schema::table('forecasts', function (Blueprint $table) {
            $table->index('item_id');
            $table->index(['item_id', 'metode']);
            $table->index(['tahun_prediksi', 'bulan_prediksi']);
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['stok']);
            $table->dropIndex(['stok', 'stok_minimum']);
        });

        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['item_id', 'tanggal']);
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['item_id', 'tanggal']);
        });

        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['item_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('procurements', function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('demand_histories', function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['item_id', 'tahun', 'bulan']);
        });

        Schema::table('eoq_calculations', function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['tanggal_hitung']);
        });

        Schema::table('forecasts', function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['item_id', 'metode']);
            $table->dropIndex(['tahun_prediksi', 'bulan_prediksi']);
        });
    }
};