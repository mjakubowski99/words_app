<?php

declare(strict_types=1);

namespace User\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\UserId;
use User\Domain\Contracts\ICreateReport;
use User\Infrastructure\Entities\Report;
use User\Application\Repositories\IReportRepository;

class ReportRepository implements IReportRepository
{
    public function __construct(
        private Report $report
    ) {}

    public function store(ICreateReport $ticket): void
    {
        $this->report->newQuery()->create([
            'email' => $ticket->getEmail(),
            'user_id' => $ticket->getUserId()?->getValue(),
            'type' => $ticket->getTicketType()->value,
            'description' => $ticket->getDescription(),
            'reportable_id' => $ticket->getReportableId() ? $ticket->getReportableId() : null,
            'reportable_type' => $ticket->getReportableType()?->value,
        ]);
    }

    public function detachFromUser(UserId $user_id): void
    {
        $this->report->newQuery()
            ->where('user_id', $user_id->getValue())
            ->update(['user_id' => null]);
    }
}
