<?php

namespace Flashcard\Infrastructure\Http\Request;

use Flashcard\Application\Command\CreateSession;
use Shared\Http\Request\Request;

class CreateSessionRequest extends Request
{
    public function rules(): array
    {
        return [
            'cards_per_session' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'flashcards_limit' => ['required', 'integer', 'min:1', 'max:200'],
        ];
    }

    public function toCommand(): CreateSession
    {
        return new CreateSession(
            $this->current(),
            (int) $this->input('cards_per_session'),
            $this->userAgent(),
            (string) $this->input('category_id')
        );
    }

    public function getFlashcardsLimit(): int
    {
        return $this->input('flashcards_limit');
    }
}