<?php

namespace Flashcard\Infrastructure\Http\Request\v2;

use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Shared\Http\Request\Request;

use OpenApi\Attributes as OAT;

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