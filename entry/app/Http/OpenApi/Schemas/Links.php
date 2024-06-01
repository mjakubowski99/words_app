<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Links',
    description: 'Links',
    properties: [
        new OAT\Property(
            property: 'links',
            properties: [
                new OAT\Property(
                    property: 'first',
                    type: 'string',
                    example: 'https://some_path/some-list?page=1',
                ),
                new OAT\Property(
                    property: 'last',
                    type: 'string',
                    example: 'https://some_path/some-list?page=5',
                ),
                new OAT\Property(
                    property: 'prev',
                    type: 'string',
                    example: 'https://some_path/some-list?page=1',
                    nullable: true,
                ),
                new OAT\Property(
                    property: 'next',
                    type: 'string',
                    example: 'https://some_path/some-list?page=3',
                    nullable: true,
                ),
            ],
            type: 'object',
        ),
    ],
    type: 'object',
)]
class Links {}
