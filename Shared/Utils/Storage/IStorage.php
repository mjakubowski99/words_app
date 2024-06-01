<?php

declare(strict_types=1);

namespace Shared\Utils\Storage;

interface IStorage
{
    public function url(string $path): string;
}
