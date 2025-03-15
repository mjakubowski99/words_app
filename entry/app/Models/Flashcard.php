<?php

declare(strict_types=1);

namespace App\Models;

use Shared\Models\Emoji;
use Shared\Enum\LanguageLevel;
use Illuminate\Database\Eloquent\Model;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property User          $user
 * @property FlashcardDeck $deck
 */
class Flashcard extends Model
{
    use HasFactory;

    public function getId(): FlashcardId
    {
        return new FlashcardId($this->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deck(): BelongsTo
    {
        return $this->belongsTo(FlashcardDeck::class, 'flashcard_deck_id');
    }

    public function toDomainModel(): \Flashcard\Domain\Models\Flashcard
    {
        return new \Flashcard\Domain\Models\Flashcard(
            $this->getId(),
            $this->front_word,
            Language::from($this->front_lang),
            $this->back_word,
            Language::from($this->back_lang),
            $this->front_context,
            $this->back_context,
            $this->user->toOwner(),
            $this->deck->toDomainModel(),
            LanguageLevel::from($this->deck->default_language_level),
            $this->emoji ? Emoji::fromUnicode($this->emoji) : null,
        );
    }
}
