<?php

namespace User\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\UserId;
use User\Application\Repositories\ITicketRepository;
use User\Domain\Contracts\INewTicket;
use User\Infrastructure\Entities\Ticket;

class TicketRepository implements ITicketRepository
{
    public function __construct(
        private Ticket $ticket
    ) {}

    public function store(INewTicket $ticket): void
    {
        $this->ticket->newQuery()->create([
            'email' => $ticket->getEmail(),
            'user_id' => $ticket->getUserId()?->getValue(),
            'type' => $ticket->getTicketType()->value,
            'description' => $ticket->getDescription(),
            'context' => $ticket->getReportableId() ? [
                'reportable_id' => $ticket->getReportableId(),
                'reportable_type' => $ticket->getReportableType()->value,
            ] : null,
        ]);
    }

    public function detachFromUser(UserId $user_id): void
    {
        $this->ticket->newQuery()
            ->where('user_id', $user_id->getValue())
            ->update(['user_id' => null]);
    }
}