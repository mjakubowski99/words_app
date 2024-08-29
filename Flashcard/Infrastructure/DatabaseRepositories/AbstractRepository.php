<?php

namespace Flashcard\Infrastructure\DatabaseRepositories;

abstract class AbstractRepository
{
    public function dbPrefix(string $table, array $columns): array
    {
        return array_map(function ($column) use ($table) {
            return "{$table}.{$column} as {$table}_{$column}";
        }, $columns);
    }
}