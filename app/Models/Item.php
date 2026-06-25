<?php
// app/Models/Item.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Item extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'category_id',
        'supplier_id',
        'satuan',
        'stok',
        'stok_minimum',
        'safety_stock',
        'harga_beli',
        'harga_jual',
        'ordering_cost',
        'holding_cost',
        'lead_time',
        'foto',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'stok'          => 'integer',
        'stok_minimum'  => 'integer',
        'safety_stock'  => 'integer',
        'harga_beli'    => 'decimal:2',
        'harga_jual'    => 'decimal:2',
        'ordering_cost' => 'decimal:2',
        'holding_cost'  => 'decimal:2',
        'lead_time'     => 'integer',
        'is_active'     => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_barang', 'stok', 'stok_minimum', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('item');
    }

    // ==================
    // RELASI
    // ==================
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class, 'item_id');
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class, 'item_id');
    }

    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class, 'item_id');
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class, 'item_id');
    }

    public function demandHistories(): HasMany
    {
        return $this->hasMany(DemandHistory::class, 'item_id');
    }

    public function eoqCalculations(): HasMany
    {
        return $this->hasMany(EoqCalculation::class, 'item_id');
    }

    public function forecasts(): HasMany
    {
        return $this->hasMany(Forecast::class, 'item_id');
    }

    // ==================
    // SCOPE
    // ==================
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stok', '<=', 'stok_minimum');
    }

    public function scopeNeedReorder($query)
    {
        // Stok sudah mencapai atau di bawah safety_stock + ROP
        return $query->whereColumn('stok', '<=', 'safety_stock');
    }

    // ==================
    // ACCESSOR
    // ==================

    // Status stok
    public function getStatusStokAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'habis';
        } elseif ($this->stok <= $this->stok_minimum) {
            return 'menipis';
        }
        return 'aman';
    }

    // Badge warna status stok
    public function getStatusStokColorAttribute(): string
    {
        return match($this->status_stok) {
            'habis'   => 'red',
            'menipis' => 'yellow',
            'aman'    => 'green',
        };
    }

    // URL foto
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/no-image.png');
    }
}