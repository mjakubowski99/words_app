<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Flashcard\Application\Command\CreateSession;
use Flashcard\Domain\ValueObjects\CategoryId;
use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;

#[OAT\Schema(
    schema: 'Requests\Flashcard\CreateSessionRequest',
    properties: [
        new OAT\Property(
            property: 'category_id',
            description: 'Flashcards category id',
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
            'cards_per_session' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
        ];
    }

    public function toCommand(): CreateSession
    {
        return new CreateSession(
            $this->current(),
            (int) $this->input('cards_per_session'),
            $this->userAgent(),
            new CategoryId((int) $this->input('category_id')),
        );
    }
}
