<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;

class GetFlashcardByUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['integer', 'gte:0'],
            'per_page' => ['integer', 'gte:0', 'lte:30'],
        ];
    }

    public function toOwner(): Owner
    {
        return new Owner(
            new OwnerId($this->currentId()->getValue()),
            FlashcardOwnerType::USER
        );
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
