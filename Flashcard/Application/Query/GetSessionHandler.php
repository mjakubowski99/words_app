<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\SessionRead;
use Flashcard\Application\Repository\ISessionReadRepository;

class GetSessionHandler
{
    public function __construct(
        private readonly ISessionReadRepository $repository,
    ) {}

    public function handle(SessionId $id): SessionRead
    {
        return $this->repository->find($id);
    }
}
