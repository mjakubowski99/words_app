<?php

declare(strict_types=1);

namespace User\Application\Repositories;

use Shared\Utils\ValueObjects\UserId;
use User\Domain\Contracts\ICreateReport;

interface IReportRepository
{
    public function store(ICreateReport $ticket): void;

    public function detachFromUser(UserId $user_id): void;
}
