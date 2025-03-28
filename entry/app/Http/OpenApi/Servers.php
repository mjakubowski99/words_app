<?php

declare(strict_types=1);

namespace App\Http\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\Server(
    url: 'http://localhost:8001',
    description: 'localhost'
)]
#[OAT\Server(
    url: 'https://api.vocasmart.pl',
    description: 'api.vocasmart.pl'
)]
class Servers {}
