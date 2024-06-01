<?php

declare(strict_types=1);

namespace Shared\Utils\Container;

use Illuminate\Container\Container as FrameworkContainer;

class Container implements IContainer
{
    public function __construct(
        private readonly FrameworkContainer $container
    ) {}

    public function make(string $class): object
    {
        return $this->container->make($class);
    }
}
