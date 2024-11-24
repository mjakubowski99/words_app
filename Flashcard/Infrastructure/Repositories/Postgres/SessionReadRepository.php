<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Application\ReadModels\SessionRead;
use Flashcard\Application\Repository\ISessionReadRepository;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Mappers\Postgres\SessionReadMapper;

class SessionReadRepository implements ISessionReadRepository
{
    public function __construct(private SessionReadMapper $mapper) {}

    public function find(SessionId $id): SessionRead
    {
        return $this->mapper->find($id);
    }
}
