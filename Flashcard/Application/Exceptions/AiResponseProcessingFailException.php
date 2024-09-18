<?php

declare(strict_types=1);

namespace Flashcard\Application\Exceptions;

class AiResponseProcessingFailException extends \Exception
{
    protected $message = 'We were not able to retrieve response from ai generator ;(. Please try again!';
}
