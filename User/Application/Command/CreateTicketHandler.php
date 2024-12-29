<?php

declare(strict_types=1);

namespace User\Application\Command;

use User\Application\Repositories\ITicketRepository;

class CreateTicketHandler
{
    public function __construct(
        private ITicketRepository $repository
    ) {}

    public function handle(CreateTicket $command): void
    {
        $this->repository->store($command);
    }
}