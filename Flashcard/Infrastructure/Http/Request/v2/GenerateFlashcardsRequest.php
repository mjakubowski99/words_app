<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Shared\Enum\LanguageLevel;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Flashcard\Application\Command\GenerateFlashcards;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\GenerateFlashcardsRequest',
    properties: [
        new OAT\Property(
            property: 'category_name',
            description: 'Category name provided by user',
            type: 'string',
            example: 'Two people talk',
        ),
        new OAT\Property(
            property: 'language_level',
            ref: '#/components/schemas/LanguageLevel'
        ),
    ]
)]
class GenerateFlashcardsRequest extends Request
{
    public function rules(): array
    {
        return [
            'category_name' => ['required', 'string', 'min:5', 'max:40'],
            'language_level' => ['nullable', 'string'],
            'page' => ['integer', 'gte:0'],
            'per_page' => ['integer', 'gte:0', 'lte:30'],
        ];
    }

    public function getPage(): int
    {
        return (int) ($this->query('page') ?? 1);
    }

    public function getPerPage(): int
    {
        return (int) ($this->query('per_page') ?? 15);
    }

    public function getCategoryName(): string
    {
        return $this->input('category_name');
    }

    public function getLanguageLevel(): LanguageLevel
    {
        return $this->input('language_level') ? LanguageLevel::from($this->input('language_level')) : LanguageLevel::default();
    }

    public function toCommand(): GenerateFlashcards
    {
        return new GenerateFlashcards(
            Owner::fromUser($this->currentId()),
            $this->getCategoryName(),
            $this->getLanguageLevel()
        );
    }
}
