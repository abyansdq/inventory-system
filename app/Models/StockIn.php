<?php
// app/Models/StockIn.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockIn extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'no_dokumen',
        'item_id',
        'supplier_id',
        'procurement_id',
        'user_id',
        'qty',
        'harga_satuan',
        'total_harga',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'qty'          => 'integer',
        'harga_satuan' => 'decimal:2',
        'total_harga'  => 'decimal:2',
        'tanggal'      => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['no_dokumen', 'item_id', 'qty', 'tanggal'])
            ->logOnlyDirty()
            ->useLogName('stock_in');
    }

    // Relasi
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class, 'procurement_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}