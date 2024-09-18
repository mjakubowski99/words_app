<?php

declare(strict_types=1);

namespace Shared\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends ApplicationException
{
    protected $message = 'Not found';
    protected $code = Response::HTTP_BAD_REQUEST;
}
