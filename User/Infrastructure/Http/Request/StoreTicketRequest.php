<?php

namespace User\Infrastructure\Http\Request;

use Illuminate\Validation\Rule;
use Shared\Enum\ReportableType;
use Shared\Enum\TicketType;
use Shared\Http\Request\Request;
use User\Application\Command\CreateTicket;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Requests\User\StoreTicketRequest',
    properties: [
        new OAT\Property(
            property: 'email',
            description: "User provided email. This field can be empty and if user is authenticated his account email will be used here",
            type: 'string',
            example: 'email@email.com',
            nullable: true,
        ),
        new OAT\Property(
            property: 'type',
            ref: '#/components/schemas/TicketType'
        ),
        new OAT\Property(
            property: 'description',
            description: "User provided description of the report",
            type: 'string',
            example: 'This flashcard has inappropriate content',
        ),
        new OAT\Property(
            property: 'reportable_id',
            example: 1,
            nullable: true
        ),
        new OAT\Property(
            property: 'reportable_type',
            ref: '#/components/schemas/ReportableType'
        ),
    ]
)]
class StoreTicketRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => ['nullable', 'string', 'max:255', 'email'],
            'type' => ['required', Rule::in(TicketType::all())],
            'description' => ['required', 'string', 'min:5'],
            'reportable_id' => ['nullable'],
            'reportable_type' => ['nullable', Rule::in(ReportableType::all())]
        ];
    }

    public function toCommand(): CreateTicket
    {
        return new CreateTicket(
            $this->user() ? $this->currentId() : null,
            $this->input('email') ? $this->input('email') : ($this->user() ? $this->current()->getEmail() : null),
            TicketType::from($this->input('type')),
            $this->input('description'),
            $this->input('reportable_id') ? (string) $this->input('reportable_id') : null,
            $this->input('reportable_type') ? ReportableType::from($this->input('reportable_type')) : null,
        );
    }
}