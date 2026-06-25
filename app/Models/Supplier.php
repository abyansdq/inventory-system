<?php
// app/Models/Supplier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'contact_person',
        'email',
        'telepon',
        'alamat',
        'kota',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_supplier', 'email', 'telepon', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('supplier');
    }

    // Relasi
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'supplier_id');
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class, 'supplier_id');
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class, 'supplier_id');
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}