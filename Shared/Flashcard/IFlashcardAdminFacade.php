<?php

declare(strict_types=1);

namespace Shared\Flashcard;

use Shared\Enum\LanguageLevel;

interface IFlashcardAdminFacade
{
    public function importDeck(
        string $admin_id,
        string $deck_name,
        LanguageLevel $level,
        array $flashcard_rows
    );

    public function delete(int $flashcard_id): void;

    public function update(
        int $flashcard_id,
        string $front_word,
        string $front_context,
        string $back_word,
        string $back_context,
    ): void;
}
