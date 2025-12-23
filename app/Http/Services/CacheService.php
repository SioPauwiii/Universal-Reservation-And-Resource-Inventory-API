<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function storeInCache(string $key, mixed $value, int $ttl): void
    {
        Cache::store('redis')->put($key, $value, $ttl);
    }

    public function getFromCache(string $key): mixed
    {
        return Cache::store('redis')->get($key);
    }

    public function clearCache(string $key): void
    {
        Cache::store('redis')->forget($key);

        return ;
    }

    public function clearAllCache(): void
    {
        Cache::store('redis')->clear();

        return ;
    }
}