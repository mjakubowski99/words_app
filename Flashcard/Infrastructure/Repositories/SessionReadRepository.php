<?php

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Application\ReadModels\SessionRead;
use Flashcard\Application\Repository\ISessionReadRepository;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Mappers\SessionReadMapper;

class SessionReadRepository implements ISessionReadRepository
{
    public function __construct(private SessionReadMapper $mapper) {}

    public function find(SessionId $id): SessionRead
    {
        return $this->mapper->find($id);
    }
}