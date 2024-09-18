<?php

declare(strict_types=1);

namespace Shared\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends ApplicationException
{
    protected $message = 'Not found';
    protected $code = Response::HTTP_NOT_FOUND;
}
