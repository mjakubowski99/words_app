<?php

declare(strict_types=1);

namespace Shared\Utils\Csv;

class Csv
{
    use HasCsvParser;

    public function __construct(
        private readonly array $data
    ) {}

    public static function fromString(string $csv_string, string $column_separator = ",", string $line_separator = "\n"): self
    {
        return new self(self::parseCsv($csv_string, $column_separator, $line_separator));
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getColumns(): array
    {
        return !empty($this->data) ? array_keys($this->data) : [];
    }

    public function getRecords(string $column): array
    {
        if (!array_key_exists($column, $this->data)) {
            throw new \UnexpectedValueException("Column not found");
        }
        return $this->data[$column];
    }

    public function get(string $key, int $index): mixed
    {
        return $this->data[$key][$index];
    }

    public function getRow(int $index): mixed
    {
        $result = [];
        foreach ($this->getColumns() as $column) {
            $result[$column] = $result[$column][$index];
        }

        return $result;
    }


    public function getRowsCount(): int
    {
        $columns = $this->getColumns();
        if (count($columns) === 0) {
            return 0;
        }
        return count($this->data[$columns[0]]);
    }
}