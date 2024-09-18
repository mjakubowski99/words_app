<?php

declare(strict_types=1);

namespace Shared\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ForbiddenException extends ApplicationException
{
    protected $message = 'Unauthorized';
    protected $code = Response::HTTP_FORBIDDEN;
}
