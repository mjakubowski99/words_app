<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;

class RegenerateFlashcardsRequest extends Request
{
    public function getOwner(): Owner
    {
        return Owner::fromUser($this->current()->getId());
    }

    public function getCategoryId(): CategoryId
    {
        return new CategoryId((int) $this->route('category_id'));
    }
}
