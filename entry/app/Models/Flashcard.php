<?php

namespace App\Models;

use Flashcard\Domain\Models\FlashcardId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Shared\Utils\ValueObjects\Language;

class Flashcard extends Model
{
    use HasFactory;

    public function getId(): FlashcardId
    {
        return new FlashcardId($this->id);
    }

    public function toDomainModel(): \Flashcard\Domain\Models\Flashcard
    {
        return new \Flashcard\Domain\Models\Flashcard(
            $this->getId(),
            $this->word,
            Language::from($this->word_lang),
            $this->translation,
            Language::from($this->translation_lang),
            $this->context,
            $this->context_translation,
        );
    }
}
