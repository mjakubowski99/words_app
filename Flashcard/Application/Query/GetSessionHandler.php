<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Application\DTO\SessionDetailsDTO;
use Flashcard\Domain\Models\SessionId;

class GetSessionHandler
{
    public function handle(SessionId $id): SessionDetailsDTO
    {

    }
}