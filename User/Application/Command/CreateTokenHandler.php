<?php

declare(strict_types=1);

namespace User\Application\Command;

use Shared\Utils\ValueObjects\UserId;
use User\Domain\Repositories\ITokenRepository;

class CreateTokenHandler
{
    public function __construct(
        private ITokenRepository $repository
    ) {}

    public function handle(UserId $user_id): string
    {
        return $this->repository->create($user_id);
    }
}
