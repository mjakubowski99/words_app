<?php

declare(strict_types=1);

namespace App\Models;

use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Illuminate\Database\Eloquent\Model;
use Flashcard\Domain\ValueObjects\OwnerId;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;
    use HasUuids;

    public function toOwner(): Owner
    {
        return new Owner(new OwnerId((string) $this->id), FlashcardOwnerType::ADMIN);
    }
}
