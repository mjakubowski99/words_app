<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Application\Command\GenerateFlashcards;

#[OAT\Schema(
    schema: 'Requests\Flashcard\GenerateFlashcardsRequest',
    properties: [
        new OAT\Property(
            property: 'category_name',
            description: 'Category name provided by user',
            type: 'string',
            example: 'Two people talk',
        ),
    ]
)]
class GenerateFlashcardsRequest extends Request
{
    public function rules(): array
    {
        return [
            'category_name' => ['required', 'string', 'min:5', 'max:40'],
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

    public function toCommand(): GenerateFlashcards
    {
        return new GenerateFlashcards(
            new Owner(new OwnerId($this->current()->getId()->getValue()), FlashcardOwnerType::USER),
            $this->getCategoryName()
        );
    }
}
