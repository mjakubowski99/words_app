<?php

declare(strict_types=1);
use Shared\Utils\Csv\Csv;

test('from string should create valid csv records', function (string $string, array $expected) {
    $this->markTestSkipped('Todo fix csv parser if needed');

    // GIVEN
    // WHEN
    $csv = Csv::fromString($string);

    // THEN
    expect(json_encode($csv->getData()))->toEqual(json_encode($expected));
})->with('dataProvider');
dataset('dataProvider', function () {
    yield 'Example 1' => [
        'string' => "word,word_lang\npolska,pl\nanglia,en",
        'expected' => [
            'word' => ['polska', 'anglia'],
            'word_lang' => ['pl', 'en'],
        ],
    ];

    yield 'Example 2 integers' => [
        'string' => "int,name\n1,x\n2,y",
        'expected' => [
            'int' => [1, 2],
            'name' => ['x', 'y'],
        ],
    ];
});
