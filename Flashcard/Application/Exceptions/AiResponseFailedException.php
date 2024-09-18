<?php

declare(strict_types=1);

namespace Flashcard\Application\Exceptions;

class AiResponseFailedException extends \Exception
{
    protected $message = 'Sorry ;( our AI was not able generate category. Please try again!';
}
