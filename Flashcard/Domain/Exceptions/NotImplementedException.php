<?php

declare(strict_types=1);

namespace Flashcard\Domain\Exceptions;

class NotImplementedException extends \Exception
{
    protected $message = 'Not implemented exception';
}
