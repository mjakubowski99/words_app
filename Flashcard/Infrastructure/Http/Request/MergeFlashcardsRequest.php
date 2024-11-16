<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;

class MergeFlashcardsRequest extends Request
{
    public function rules(): array
    {
        return [
            'new_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function getOwner(): Owner
    {
        return Owner::fromUser($this->current()->getId());
    }

    public function getNewName(): ?string
    {
        return $this->input('new_name');
    }

    public function getFromCategoryId(): CategoryId
    {
        return new CategoryId((int) $this->route('from_category_id'));
    }

    public function getToCategoryId(): CategoryId
    {
        return new CategoryId((int) $this->route('to_category_id'));
    }
}
