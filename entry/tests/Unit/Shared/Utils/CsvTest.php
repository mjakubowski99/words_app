<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Utils;

use Shared\Utils\Csv\Csv;
use Tests\TestCase;

class CsvTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @test
     */
    public function fromString_ShouldCreateValidCsvRecords(string $string, array $expected): void
    {
        // GIVEN

        // WHEN
        $csv = Csv::fromString($string);

        // THEN
        $this->assertEquals(json_encode($expected), json_encode($csv->getData()));
    }

    public static function dataProvider()
    {
        yield 'Example 1' => [
            'string' => "word,word_lang\npolska,pl\nanglia,en",
            'expected' => [
                'word' => ['polska', 'anglia'],
                'word_lang' => ['pl', 'en']
            ]
        ];

        yield 'Example 2 integers' => [
            'string' => "int,name\n1,x\n2,y",
            'expected' => [
                'int' => [1, 2],
                'name' => ['x', 'y']
            ]
        ];
    }
}
