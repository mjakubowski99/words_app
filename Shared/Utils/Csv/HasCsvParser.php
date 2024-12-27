<?php

declare(strict_types=1);

namespace Shared\Utils\Csv;

trait HasCsvParser
{
    public static function parseCsv(string $csv_string, string $column_separator = ',', string $line_separator = "\n"): array
    {
        $lines = explode($line_separator, $csv_string);

        $keys = str_getcsv($lines[0], $column_separator);

        $results = [];

        for ($i = 1; $i < count($lines); ++$i) {
            $records = str_getcsv(str_replace($lines[$i], "\n", ''), $column_separator);

            for ($j = 0; $j < count($keys); ++$j) {
                $key = $keys[$j];
                if (!array_key_exists($key, $results)) {
                    $results[$key] = [];
                }

                $record = array_key_exists($j, $records) ? $records[$j] : null;

                if (!$record) {
                    continue;
                }

                $parse = json_decode('[' . $record . ']', true);

                $results[$key][] = is_array($parse) ? $parse[0] : $record;
            }
        }

        return $results;
    }
}
