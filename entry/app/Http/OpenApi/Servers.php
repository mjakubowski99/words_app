<?php

declare(strict_types=1);

namespace App\Http\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\Server(
    url: 'http://localhost',
    description: 'localhost'
)]
class Servers {}
