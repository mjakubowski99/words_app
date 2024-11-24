<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Shared\Enum\LearningSessionType;
use Flashcard\Application\Command\CreateSession;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\CreateSessionRequest',
    properties: [
        new OAT\Property(
            property: 'flashcard_deck_id',
            description: 'Flashcard deck id',
            example: 1,
        ),
        new OAT\Property(
            property: 'cards_per_session',
            description: 'Cards per learning session',
            example: 10,
        ),
    ]
)]
class CreateSessionRequest extends Request
{
    public function rules(): array
    {
        return [
            'cards_per_session' => ['required', 'integer', 'gte:5', 'lte:100'],
            'flashcard_deck_id' => ['nullable', 'integer'],
        ];
    }

    public function toCommand(): CreateSession
    {
        return new CreateSession(
            $this->current(),
            (int) $this->input('cards_per_session'),
            $this->userAgent(),
            $this->input('flashcard_deck_id') !== null ? new FlashcardDeckId((int) $this->input('flashcard_deck_id')) : null,
            $this->input('flashcard_deck_id') !== null ?
                LearningSessionType::LEARN_FLASHCARDS_IN_CATEGORY
                : LearningSessionType::LEARN_YOUR_ALL_FLASHCARDS,
        );
    }
}
