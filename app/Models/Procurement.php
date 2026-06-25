<?php
// app/Models/Procurement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Procurement extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'no_pengadaan',
        'item_id',
        'supplier_id',
        'user_id',
        'qty',
        'harga_satuan',
        'total_harga',
        'tanggal',
        'tanggal_dibutuhkan',
        'status',
        'approved_by',
        'approved_at',
        'catatan',
        'no_dokumen_referensi',
    ];

    protected $casts = [
        'qty'                => 'integer',
        'harga_satuan'       => 'decimal:2',
        'total_harga'        => 'decimal:2',
        'tanggal'            => 'date',
        'tanggal_dibutuhkan' => 'date',
        'approved_at'        => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['no_pengadaan', 'status', 'approved_by', 'qty'])
            ->logOnlyDirty()
            ->useLogName('procurement');
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function stockIn(): HasOne
    {
        return $this->hasOne(StockIn::class, 'procurement_id');
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
            'draft'     => 'gray',
            'pending'   => 'yellow',
            'approved'  => 'green',
            'rejected'  => 'red',
            'ordered'   => 'blue',
            'received'  => 'indigo',
            'cancelled' => 'red',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Draft',
            'pending'   => 'Menunggu Persetujuan',
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            'ordered'   => 'Sudah Dipesan',
            'received'  => 'Sudah Diterima',
            'cancelled' => 'Dibatalkan',
        };
    }
}