<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use Shared\Enum\LanguageLevel;
use Illuminate\Validation\Rule;
use Shared\Http\Request\Request;

class IndexFlashcardDeckRequest extends Request
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'language_level' => ['nullable', 'string', Rule::enum(LanguageLevel::class)],
            'page' => ['integer', 'gte:0'],
            'per_page' => ['integer', 'gte:0', 'lte:30'],
        ];
    }

    public function getLanguageLevel(): ?LanguageLevel
    {
        return $this->input('language_level') ? LanguageLevel::from($this->input('language_level')) : null;
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
