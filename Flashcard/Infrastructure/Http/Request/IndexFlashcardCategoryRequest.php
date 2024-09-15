<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Shared\Http\Request\Request;
use Shared\Utils\ValueObjects\UserId;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Requests\Flashcard\IndexFlashcardCategoryRequest',
    properties: [
        new OAT\Property(
            property: 'page',
            description: 'Categories page number',
            example: 1,
        ),
        new OAT\Property(
            property: 'per_page',
            description: 'Categories per page',
            example: 10,
        ),
    ]
)]
class IndexFlashcardCategoryRequest extends Request
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'integer', 'gte:0'],
            'per_page' => ['required', 'integer', 'max:30'],
        ];
    }

    public function getUserId(): UserId
    {
        return $this->current()->getId();
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
