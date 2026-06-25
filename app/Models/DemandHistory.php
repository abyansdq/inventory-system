<?php
// app/Models/DemandHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'tahun',
        'bulan',
        'jumlah_permintaan',
        'keterangan',
    ];

    protected $casts = [
        'tahun'              => 'integer',
        'bulan'              => 'integer',
        'jumlah_permintaan'  => 'integer',
    ];

    // Relasi
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Accessor
    public function getNamaBulanAttribute(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',      6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
            10 => 'Oktober',11 => 'November',12 => 'Desember',
        ];
        return $bulan[$this->bulan] ?? '-';
    }

    public function getPeriodeAttribute(): string
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    // Scope
    public function scopeByItem($query, int $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeByYear($query, int $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeOrderByPeriode($query)
    {
        return $query->orderBy('tahun')->orderBy('bulan');
    }
}