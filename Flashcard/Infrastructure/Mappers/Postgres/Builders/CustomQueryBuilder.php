<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres\Builders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

abstract class CustomQueryBuilder extends Builder
{
    abstract public static function tableName(): string;

    public static function new(): static
    {
        return static::table(static::tableName());
    }

    public static function table(string $table): static
    {
        $connection = DB::connection();

        /* @phpstan-ignore-next-line */
        return (new static(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        ))->from($table);
    }

    public function addSelectAll(array $rows): static
    {
        $t = static::tableName();

        return $this->addSelect(array_map(fn ($r) => "{$t}.{$r}", $rows));
    }

    public function setPage(int $page, int $per_page): static
    {
        return $this->take($per_page)
            ->skip(($page - 1) * $per_page);
    }
}
