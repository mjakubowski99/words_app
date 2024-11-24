<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\SortCriteria\Postgres;

abstract class PostgresSortCriteria
{
    abstract public function apply(): string;
}
