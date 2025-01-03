<?php

declare(strict_types=1);

namespace Admin\Policies;

use App\Models\Admin;
use Admin\Models\Flashcard;
use App\Models\LearningSessionFlashcard;

class FlashcardPolicy
{
    public function viewAny(Admin $admin)
    {
        return true;
    }

    public function create(Admin $admin)
    {
        return false;
    }

    public function update(Admin $admin, Flashcard $flashcard)
    {
        return true;
    }

    public function delete(Admin $admin, Flashcard $flashcard)
    {
        return !LearningSessionFlashcard::query()
            ->where('flashcard_id', $flashcard->id)
            ->exists();
    }

    public function forceDelete(Admin $admin, Flashcard $flashcard)
    {
        return $this->delete($admin, $flashcard);
    }

    public function forceDeleteAny(Admin $admin)
    {
        return false;
    }

    public function restore(Admin $admin, Flashcard $flashcard)
    {
        return false;
    }

    public function reorder(Admin $admin)
    {
        return false;
    }
}
