<?php
// app/Observers/ItemObserver.php

namespace App\Observers;

use App\Models\Item;
use App\Services\CacheService;

class ItemObserver
{
    public function __construct(private CacheService $cache) {}

    public function created(Item $item): void
    {
        $this->cache->clearItemCache();
    }

    public function updated(Item $item): void
    {
        $this->cache->clearItemCache();
    }

    public function deleted(Item $item): void
    {
        $this->cache->clearItemCache();
    }
}