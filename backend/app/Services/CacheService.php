<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    public function put(string $key, $value, $seconds): void
    {
        Cache::put($key, $value, $seconds);
    }

    public function remember(string $key, $seconds, callable $callback)
    {
        return Cache::remember($key, $seconds, $callback);
    }

    public function rememberForever(string $key, callable $callback)
    {
        return Cache::rememberForever($key, $callback);
    }

    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    public function flush(): void
    {
        Cache::flush();
    }
}
