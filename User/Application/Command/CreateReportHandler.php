<?php

declare(strict_types=1);

namespace User\Application\Command;

use User\Application\Repositories\IReportRepository;

class CreateReportHandler
{
    public function __construct(
        private IReportRepository $repository
    ) {}

    public function handle(CreateReport $command): void
    {
        $this->repository->store($command);
    }
}
