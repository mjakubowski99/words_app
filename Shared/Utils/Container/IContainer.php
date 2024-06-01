<?php

declare(strict_types=1);

namespace Shared\Utils\Container;

interface IContainer
{
    /**
     * @template T
     *
     * @return T
     */
    public function make(string $class): mixed;
}
