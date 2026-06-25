<?php
// app/Models/Forecast.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Forecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'generated_by',
        'metode',
        'periode_bulan',
        'tahun_prediksi',
        'bulan_prediksi',
        'hasil_prediksi',
        'actual_demand',
        'error_mae',
        'error_mape',
        'bobot',
        'keterangan',
    ];

    protected $casts = [
        'periode_bulan'  => 'integer',
        'tahun_prediksi' => 'integer',
        'bulan_prediksi' => 'integer',
        'hasil_prediksi' => 'decimal:2',
        'actual_demand'  => 'decimal:2',
        'error_mae'      => 'decimal:4',
        'error_mape'     => 'decimal:4',
        'bobot'          => 'array',
    ];

    // Relasi
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Accessor
    public function getNamaBulanPrediksiAttribute(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',      6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
            10 => 'Oktober',11 => 'November',12 => 'Desember',
        ];
        return $bulan[$this->bulan_prediksi] ?? '-';
    }

    public function getPeriodePrediksiAttribute(): string
    {
        return $this->nama_bulan_prediksi . ' ' . $this->tahun_prediksi;
    }

    public function getMetodeLabelAttribute(): string
    {
        return match($this->metode) {
            'moving_average'           => 'Moving Average',
            'weighted_moving_average'  => 'Weighted Moving Average',
            'linear_regression'        => 'Linear Regression',
        };
    }

    // Scope
    public function scopeByItem($query, int $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeByMetode($query, string $metode)
    {
        return $query->where('metode', $metode);
    }
}