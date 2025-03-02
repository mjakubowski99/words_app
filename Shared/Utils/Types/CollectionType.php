<?php

namespace Shared\Utils\Types;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CollectionType
{
    public function __construct(public string $type) {}
}