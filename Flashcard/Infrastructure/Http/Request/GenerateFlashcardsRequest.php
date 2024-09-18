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
            example: 1,
        ),
    ]
)]
class GenerateFlashcardsRequest extends Request
{
    public function rules(): array
    {
        return [
            'category_name' => ['required', 'string', 'min:5', 'max:40'],
        ];
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
