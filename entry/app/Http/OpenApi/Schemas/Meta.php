<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Meta',
    description: 'Meta',
    properties: [
        new OAT\Property(
            property: 'meta',
            properties: [
                new OAT\Property(
                    property: 'current_page',
                    type: 'integer',
                    example: 2,
                ),
                new OAT\Property(
                    property: 'from',
                    type: 'integer',
                    example: 21,
                ),
                new OAT\Property(
                    property: 'last_page',
                    type: 'integer',
                    example: 5,
                ),
                new OAT\Property(
                    property: 'path',
                    type: 'string',
                    example: 'https://some_path/some-list',
                ),
                new OAT\Property(
                    property: 'per_page',
                    type: 'integer',
                    example: 20,
                ),
                new OAT\Property(
                    property: 'to',
                    type: 'integer',
                    example: 40,
                ),
                new OAT\Property(
                    property: 'total',
                    type: 'integer',
                    example: 98,
                ),
            ],
            type: 'object',
        ),
    ],
    type: 'object',
)]
class Meta {}
