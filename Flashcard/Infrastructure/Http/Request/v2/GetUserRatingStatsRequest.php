<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Illuminate\Validation\Rule;
use Shared\Http\Request\Request;
use Shared\Enum\FlashcardOwnerType;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\GetUserRatingStatsRequest',
    properties: [
        new OAT\Property(
            property: 'owner_type',
            ref: '#/components/schemas/FlashcardOwnerType'
        ),
    ]
)]
class GetUserRatingStatsRequest extends Request
{
    public function rules(): array
    {
        return [
            'owner_type' => ['nullable', Rule::in([FlashcardOwnerType::USER->value, FlashcardOwnerType::ADMIN->value])],
        ];
    }

    public function getOwnerType(): ?FlashcardOwnerType
    {
        return $this->input('owner_type') ? FlashcardOwnerType::from($this->input('owner_type')) : null;
    }
}
