<?php

declare(strict_types=1);

namespace Flashcard\Domain\Exceptions;

use Shared\Exceptions\ApplicationException;

class NotImplementedException extends ApplicationException
{
    protected $message = 'Not implemented exception';
}
