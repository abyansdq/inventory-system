<?php
// app/Models/StockOut.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockOut extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'no_dokumen',
        'item_id',
        'item_request_id',
        'user_id',
        'qty',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'qty'     => 'integer',
        'tanggal' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['no_dokumen', 'item_id', 'qty', 'tanggal'])
            ->logOnlyDirty()
            ->useLogName('stock_out');
    }

    // Relasi
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}