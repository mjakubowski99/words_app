<?php

declare(strict_types=1);

namespace Shared\Utils\Types;

#[\Attribute(\Attribute::TARGET_CLASS)]
class CollectionType
{
    public function __construct(public string $type) {}
}
