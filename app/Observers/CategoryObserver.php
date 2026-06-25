<?php
// app/Observers/CategoryObserver.php

namespace App\Observers;

use App\Models\Category;
use App\Services\CacheService;

class CategoryObserver
{
    public function __construct(private CacheService $cache) {}

    public function created(Category $category): void  { $this->cache->clearCategoryCache(); }
    public function updated(Category $category): void  { $this->cache->clearCategoryCache(); }
    public function deleted(Category $category): void  { $this->cache->clearCategoryCache(); }
}