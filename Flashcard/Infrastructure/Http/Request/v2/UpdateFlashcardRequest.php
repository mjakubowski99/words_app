<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Command\UpdateFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\UpdateFlashcardRequest',
    properties: [
        new OAT\Property(
            property: 'flashcard_deck_id',
            description: 'Deck id to update',
            type: 'integer',
            example: 1,
        ),
        new OAT\Property(
            property: 'front_word',
            description: 'Flashcard front word',
            type: 'string',
            example: 'Jabłko',
        ),
        new OAT\Property(
            property: 'back_word',
            description: 'Flashcard back word',
            type: 'string',
            example: 'Apple',
        ),
        new OAT\Property(
            property: 'front_context',
            description: 'Front flashcard context sentence',
            type: 'string',
            example: 'Adam je jabłko',
        ),
        new OAT\Property(
            property: 'back_context',
            description: 'Back flashcard context sentence',
            type: 'string',
            example: 'Adam eats apple',
        ),
    ]
)]
class UpdateFlashcardRequest extends Request
{
    public function rules(): array
    {
        return [
            'flashcard_deck_id' => ['required', 'integer'],
            'front_word' => ['required', 'string', 'max:255'],
            'back_word' => ['required', 'string', 'max:255'],
            'front_context' => ['required', 'string', 'max:255'],
            'back_context' => ['required', 'string', 'max:255'],
        ];
    }

    public function toCommand(): UpdateFlashcard
    {
        $user = $this->current();

        return new UpdateFlashcard(
            new FlashcardId((int) $this->route('flashcard_id')),
            Owner::fromUser($user->getId()),
            new FlashcardDeckId($this->input('flashcard_deck_id')),
            Language::pl(),
            $this->input('front_word'),
            $this->input('front_context'),
            Language::en(),
            $this->input('back_word'),
            $this->input('back_context')
        );
    }
}
