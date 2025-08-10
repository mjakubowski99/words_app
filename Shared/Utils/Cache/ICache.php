<?php

declare(strict_types=1);

namespace Shared\Utils\Cache;

interface ICache
{
    public function get(string $key): mixed;

    public function put(string $key, mixed $value, int $ttl = 60): void;
}
