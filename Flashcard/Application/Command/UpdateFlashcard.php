<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

class UpdateFlashcard extends CreateFlashcard
{
    public function __construct(
        private readonly FlashcardId $id,
        Owner $owner,
        FlashcardDeckId $deck_id,
        Language $front_lang,
        string $front_word,
        string $front_context,
        Language $back_lang,
        string $back_word,
        string $back_context,
    ) {
        parent::__construct(
            $owner,
            $deck_id,
            $front_lang,
            $front_word,
            $front_context,
            $back_lang,
            $back_word,
            $back_context
        );
    }

    public function getId(): FlashcardId
    {
        return $this->id;
    }
}
