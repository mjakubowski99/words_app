<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use Shared\Enum\SessionType;
use OpenApi\Attributes as OAT;
use Illuminate\Validation\Rule;
use Shared\Http\Request\Request;
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
        new OAT\Property(
            property: 'session_type',
            description: 'Type of the session (e.g., flashcard, unscramble_words, etc.). If not specified flashcard type is used.',
            type: 'string',
            enum: [
                SessionType::FLASHCARD,
                SessionType::UNSCRAMBLE_WORDS,
                SessionType::MIXED,
            ],
            example: 'flashcard',
            nullable: true,
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
            'session_type' => ['nullable', 'string', Rule::enum(SessionType::class)],
        ];
    }

    public function toCommand(): CreateSession
    {
        return new CreateSession(
            $this->current()->getId(),
            (int) $this->input('cards_per_session'),
            $this->userAgent(),
            $this->input('flashcard_deck_id') !== null ? new FlashcardDeckId((int) $this->input('flashcard_deck_id')) : null,
            SessionType::from($this->input('session_type', SessionType::FLASHCARD->value)),
        );
    }
}
