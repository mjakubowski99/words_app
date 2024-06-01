<?php

declare(strict_types=1);

namespace Shared\Utils\Config;

interface IConfig
{
    public function get(string $key): mixed;
}
