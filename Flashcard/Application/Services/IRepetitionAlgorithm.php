<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Domain\Contracts\IRepetitionAlgorithmDTO;

interface IRepetitionAlgorithm
{
    public function handle(IRepetitionAlgorithmDTO $dto): void;
}
