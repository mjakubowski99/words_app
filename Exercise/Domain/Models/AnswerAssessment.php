<?php

namespace Exercise\Domain\Models;

class AnswerAssessment
{
    public function __construct(
        private float $score,
    ) {}

    public function isCorrect(): bool
    {
        return $this->score >= 100;
    }

    public function getScore(): float
    {
        return $this->score;
    }
}