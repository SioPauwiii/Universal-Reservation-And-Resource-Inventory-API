<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheInspectorService
{
    public function inspect(string $key): array
    {
        $storeValue = Cache::store('redis')->get($key);

        $info = [
            'key' => $key,
            'value_in_cache_store' => $storeValue,
            'exists' => null,
            'ttl' => null,
            'type' => null,
            'raw_value' => null,
            'error' => null,
        ];

        try {
            $conn = Redis::connection();

            $exists = $conn->exists($key);
            $info['exists'] = (bool) $exists;

            $info['ttl'] = $conn->ttl($key);

            try {
                $info['type'] = $conn->type($key);
            } catch (\Exception $e) {
                $info['type'] = null;
            }

            try {
                $info['raw_value'] = $conn->get($key);
            } catch (\Exception $e) {
                $info['raw_value'] = null;
            }
        } catch (\Exception $e) {
            $info['error'] = $e->getMessage();
        }

        return $info;
    }
}
