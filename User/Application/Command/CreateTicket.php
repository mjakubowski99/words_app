<?php

declare(strict_types=1);

namespace User\Application\Command;

use Shared\Enum\ReportableType;
use Shared\Enum\TicketType;
use Shared\Utils\ValueObjects\UserId;
use User\Domain\Contracts\INewTicket;

class CreateTicket implements INewTicket
{
    public function __construct(
        private ?UserId $user_id,
        private ?string $email,
        private TicketType $type,
        private string $description,
        private ?string $reportable_id=null,
        private ?ReportableType $reportable_type=null,
    ) {
        if (!is_null($this->reportable_id) && is_null($this->reportable_type)) {
            throw new \UnexpectedValueException('Reportable id cannot be null when reportable type is null');
        }
        if (is_null($this->reportable_id) && !is_null($this->reportable_type)) {
            throw new \UnexpectedValueException('Reportable type cannot be null when reportable id is null');
        }
    }

    public function getUserId(): ?UserId
    {
        return $this->user_id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTicketType(): TicketType
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getReportableId(): ?string
    {
        return $this->reportable_id;
    }

    public function getReportableType(): ?ReportableType
    {
        return $this->reportable_type;
    }
}