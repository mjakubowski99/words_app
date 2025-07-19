<?php

namespace Shared\Utils\Cache;
class Cache implements ICache
{
    public function get(string $key): mixed
    {
        return \Illuminate\Support\Facades\Cache::get($key);
    }

    public function put(string $key, mixed $value, int $ttl = 60): void
    {
        \Illuminate\Support\Facades\Cache::put($key, $value, $ttl);
    }
}