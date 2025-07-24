<?php

namespace Flashcard\Application\DTO;

use Shared\Flashcard\IAnswerOption;

class AnswerOption implements IAnswerOption
{
    public function __construct(private string $option)
    {

    }

    public function getOption(): string
    {
        return $this->option;
    }
}