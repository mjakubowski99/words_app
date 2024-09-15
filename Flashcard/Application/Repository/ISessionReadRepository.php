<?php

namespace Flashcard\Application\Repository;
use Flashcard\Application\ReadModels\SessionRead;
use Flashcard\Domain\ValueObjects\SessionId;

interface ISessionReadRepository
{
    public function find(SessionId $id): SessionRead;
}