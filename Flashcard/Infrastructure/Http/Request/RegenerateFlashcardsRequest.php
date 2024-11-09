<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;

class RegenerateFlashcardsRequest extends Request
{
    public function rules(): array
    {
        return [
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

    public function getOwner(): Owner
    {
        return Owner::fromUser($this->current()->getId());
    }

    public function getCategoryId(): CategoryId
    {
        return new CategoryId((int) $this->route('category_id'));
    }
}
