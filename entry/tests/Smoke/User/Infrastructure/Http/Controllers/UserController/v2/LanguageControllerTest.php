<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('get languages success', function () {
    // GIVEN
    // WHEN
    $response = $this->getJson(route('languages.get'));

    // THEN
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'code',
                'flag',
            ],
        ]
    ]);
});
