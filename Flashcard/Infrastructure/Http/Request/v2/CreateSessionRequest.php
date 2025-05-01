<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use Illuminate\Validation\Rule;
use OpenApi\Attributes as OAT;
use Shared\Enum\SessionType;
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
