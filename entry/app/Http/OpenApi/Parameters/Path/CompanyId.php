<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Parameters\Path;

use OpenApi\Attributes as OAT;

#[OAT\Parameter(
    parameter: 'Path\CompanyId',
    name: 'company_id',
    description: 'Shop identifier',
    in: 'path',
    required: true,
    schema: new OAT\Schema(
        type: 'integer',
    ),
)]
class CompanyId {}
