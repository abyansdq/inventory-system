<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'nama_kategori',
        'kode_kategori',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_kategori', 'kode_kategori', 'is_active'])
            ->logOnlyDirty()
            ->useLogName('category');
    }

    // Relasi
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}