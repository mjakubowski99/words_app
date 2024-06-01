<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Path;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'Path\CashierNumber',
    name: 'cashier_number',
    description: 'Cashier identifier',
    in: 'path',
    required: true,
    schema: new OAT\Schema(
        type: 'string',
    ),
)]
class CashierNumber {}
