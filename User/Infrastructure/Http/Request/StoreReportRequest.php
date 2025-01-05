<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Request;

use Shared\Enum\ReportType;
use OpenApi\Attributes as OAT;
use Illuminate\Validation\Rule;
use Shared\Enum\ReportableType;
use Shared\Http\Request\Request;
use User\Application\Command\CreateReport;

#[OAT\Schema(
    schema: 'Requests\User\StoreReportRequest',
    properties: [
        new OAT\Property(
            property: 'email',
            description: 'User provided email. This field can be empty and if user is authenticated his account email will be used here',
            type: 'string',
            example: 'email@email.com',
            nullable: true,
        ),
        new OAT\Property(
            property: 'type',
            ref: '#/components/schemas/ReportType'
        ),
        new OAT\Property(
            property: 'description',
            description: 'User provided description of the report',
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
class StoreReportRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => ['nullable', 'string', 'max:255', 'email'],
            'type' => ['required', Rule::in(ReportType::all())],
            'description' => ['required', 'string', 'min:5'],
            'reportable_id' => ['nullable'],
            'reportable_type' => ['nullable', Rule::in(ReportableType::all())],
        ];
    }

    public function toCommand(): CreateReport
    {
        return new CreateReport(
            $this->user() ? $this->currentId() : null,
            $this->input('email') ? $this->input('email') : ($this->user() ? $this->current()->getEmail() : null),
            ReportType::from($this->input('type')),
            $this->input('description'),
            $this->input('reportable_id') ? (string) $this->input('reportable_id') : null,
            $this->input('reportable_type') ? ReportableType::from($this->input('reportable_type')) : null,
        );
    }
}
