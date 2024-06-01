<?php

declare(strict_types=1);

namespace Shared\Utils\Storage;

use Illuminate\Support\Facades\Storage as IlluminateStorage;

class Storage implements IStorage
{
    public function __construct(
        private readonly IlluminateStorage $storage
    ) {}

    public function url(string $path): string
    {
        return $this->storage::disk('local')->get($path);
    }
}
