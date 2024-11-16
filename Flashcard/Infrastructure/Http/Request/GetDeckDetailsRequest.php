<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Shared\Http\Request\Request;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class GetDeckDetailsRequest extends Request
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['integer', 'gte:0'],
            'per_page' => ['integer', 'gte:0', 'lte:30'],
        ];
    }

    public function getDeckId(): FlashcardDeckId
    {
        return new FlashcardDeckId((int) $this->route('category_id'));
    }

    public function getSearch(): ?string
    {
        return $this->input('search');
    }

    public function getPage(): int
    {
        return (int) ($this->query('page') ?? 1);
    }

    public function getPerPage(): int
    {
        return (int) ($this->query('per_page') ?? 15);
    }
}
