<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Shared\Http\Request\Request;
use Shared\Utils\ValueObjects\UserId;

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
