<?php

declare(strict_types=1);

namespace Admin\Policies;

use App\Models\Admin;
use Admin\Models\FlashcardDeck;

class FlashcardDeckPolicy
{
    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    public function create(Admin $admin): bool
    {
        return false;
    }

    public function update(Admin $admin, FlashcardDeck $deck): bool
    {
        return true;
    }

    public function delete(Admin $admin, FlashcardDeck $deck): bool
    {
        return false;
    }

    public function forceDelete(Admin $admin, FlashcardDeck $deck): bool
    {
        return false;
    }

    public function forceDeleteAny(Admin $admin): bool
    {
        return false;
    }

    public function restore(Admin $admin, FlashcardDeck $deck): bool
    {
        return false;
    }

    public function reorder(Admin $admin): bool
    {
        return false;
    }
}
