<?php

declare(strict_types=1);

namespace Shared\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ServerErrorException extends ApplicationException
{
    protected $message = 'Server error';
    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
}
