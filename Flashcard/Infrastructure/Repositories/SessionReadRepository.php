<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\SessionRead;
use Flashcard\Infrastructure\Mappers\SessionReadMapper;
use Flashcard\Application\Repository\ISessionReadRepository;

class SessionReadRepository implements ISessionReadRepository
{
    public function __construct(private SessionReadMapper $mapper) {}

    public function find(SessionId $id): SessionRead
    {
        return $this->mapper->find($id);
    }
}
