<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\SessionRead;

interface ISessionReadRepository
{
    public function find(SessionId $id): SessionRead;
}
