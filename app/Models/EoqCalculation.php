<?php
// app/Models/EoqCalculation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EoqCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'calculated_by',
        'demand_tahunan',
        'ordering_cost',
        'holding_cost',
        'eoq_result',
        'demand_harian_avg',
        'demand_harian_max',
        'lead_time',
        'safety_stock',
        'rop_result',
        'frekuensi_pesan',
        'interval_pesan',
        'tanggal_hitung',
        'keterangan',
    ];

    protected $casts = [
        'demand_tahunan'    => 'decimal:2',
        'ordering_cost'     => 'decimal:2',
        'holding_cost'      => 'decimal:2',
        'eoq_result'        => 'decimal:2',
        'demand_harian_avg' => 'decimal:4',
        'demand_harian_max' => 'decimal:4',
        'lead_time'         => 'integer',
        'safety_stock'      => 'decimal:2',
        'rop_result'        => 'decimal:2',
        'frekuensi_pesan'   => 'decimal:2',
        'interval_pesan'    => 'decimal:2',
        'tanggal_hitung'    => 'datetime',
    ];

    // Relasi
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }
}