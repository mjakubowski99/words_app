<?php

declare(strict_types=1);

namespace App\Http\OpenApi\Responses;

use OpenApi\Attributes as OAT;

#[OAT\Response(
    response: 'dummy',
    description: 'Dummy response, to be created.',
)]
class DummyResponse {}
