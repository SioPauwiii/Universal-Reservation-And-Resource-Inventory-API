<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Redis;

class RedisLockService
{
    /**
     * Acquire a simple Redis lock using SET NX PX.
     * Returns the token string on success, or null on failure.
     * $ttlMs: lock TTL in milliseconds
     * $waitMs: total wait time to retry acquiring
     */
    public function acquire(string $key, int $ttlMs = 10000, int $waitMs = 5000, int $retryDelayMs = 100): ?string
    {
        $end = microtime(true) + ($waitMs / 1000);
        $token = bin2hex(random_bytes(8));

        while (microtime(true) < $end) {
            try {
                $res = Redis::connection()->set($key, $token, 'PX', $ttlMs, 'NX');
            } catch (\Throwable $e) {
                $res = false;
            }

            if ($res) {
                return $token;
            }

            // sleep a bit then retry
            usleep($retryDelayMs * 1000);
        }

        return null;
    }

    /**
     * Release a lock only if the token matches, using a Lua script.
     */
    public function release(string $key, string $token): bool
    {
        $script = "if redis.call('get', KEYS[1]) == ARGV[1] then return redis.call('del', KEYS[1]) else return 0 end";
        try {
            $res = Redis::connection()->eval($script, 1, $key, $token);
            return (int) $res === 1;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
