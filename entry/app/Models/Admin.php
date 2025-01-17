<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Panel;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Database\Factories\AdminFactory;
use Illuminate\Database\Eloquent\Builder;
use Flashcard\Domain\ValueObjects\OwnerId;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @method static AdminFactory          factory($count = null, $state = [])
 * @method static Builder<static>|Admin newModelQuery()
 * @method static Builder<static>|Admin newQuery()
 * @method static Builder<static>|Admin query()
 * @mixin \Eloquent
 */
class Admin extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    public function toOwner(): Owner
    {
        return new Owner(new OwnerId((string) $this->id), FlashcardOwnerType::ADMIN);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
