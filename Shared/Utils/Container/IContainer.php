<?php

declare(strict_types=1);

namespace Shared\Utils\Container;

interface IContainer
{
    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function make(string $class): mixed;
}
