<?php
// app/Observers/SupplierObserver.php

namespace App\Observers;

use App\Models\Supplier;
use App\Services\CacheService;

class SupplierObserver
{
    public function __construct(private CacheService $cache) {}

    public function created(Supplier $supplier): void  { $this->cache->clearSupplierCache(); }
    public function updated(Supplier $supplier): void  { $this->cache->clearSupplierCache(); }
    public function deleted(Supplier $supplier): void  { $this->cache->clearSupplierCache(); }
}