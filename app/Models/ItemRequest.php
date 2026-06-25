<?php
// app/Models/ItemRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ItemRequest extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'no_permintaan',
        'user_id',
        'item_id',
        'qty',
        'tanggal',
        'tanggal_butuh',
        'keperluan',
        'status',
        'approved_by',
        'approved_at',
        'catatan_admin',
    ];

    protected $casts = [
        'qty'          => 'integer',
        'tanggal'      => 'date',
        'tanggal_butuh'=> 'date',
        'approved_at'  => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['no_permintaan', 'status', 'approved_by'])
            ->logOnlyDirty()
            ->useLogName('item_request');
    }

    // Relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function stockOut(): HasOne
    {
        return $this->hasOne(StockOut::class, 'item_request_id');
    }

    // Scope
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Accessor
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'yellow',
            'approved'  => 'green',
            'rejected'  => 'red',
            'processed' => 'blue',
            'cancelled' => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'Menunggu',
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            'processed' => 'Diproses',
            'cancelled' => 'Dibatalkan',
        };
    }
}