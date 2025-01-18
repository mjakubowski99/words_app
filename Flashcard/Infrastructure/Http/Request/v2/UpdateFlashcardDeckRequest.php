<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\UpdateFlashcardDeckRequest',
    properties: [
        new OAT\Property(
            property: 'name',
            description: 'Flashcard deck name provided by user',
            type: 'string',
            example: 'Two people talk',
        ),
    ]
)]
class UpdateFlashcardDeckRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }

    public function getDeckId(): FlashcardDeckId
    {
        return new FlashcardDeckId((int) $this->route('flashcard_deck_id'));
    }

    public function getName(): string
    {
        return $this->input('name');
    }
}
