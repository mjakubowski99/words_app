<?php

declare(strict_types=1);

namespace App\Models;

use Shared\Models\Emoji;
use Illuminate\Support\Carbon;
use Shared\Enum\LanguageLevel;
use Illuminate\Database\Eloquent\Model;
use Shared\Utils\ValueObjects\Language;
use Database\Factories\FlashcardFactory;
use Illuminate\Database\Eloquent\Builder;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                       $id
 * @property        string                    $user_id
 * @property        int                       $flashcard_category_id
 * @property        string                    $front_word
 * @property        string                    $front_lang
 * @property        string                    $back_word
 * @property        string                    $back_lang
 * @property        string                    $front_context
 * @property        string                    $back_context
 * @property        null|Carbon               $created_at
 * @property        null|Carbon               $updated_at
 * @method   static FlashcardFactory          factory($count = null, $state = [])
 * @method   static Builder|Flashcard         newModelQuery()
 * @method   static Builder|Flashcard         newQuery()
 * @method   static Builder|Flashcard         query()
 * @property        null|int                  $flashcard_deck_id
 * @property        string                    $language_level
 * @property        null|FlashcardDeck        $deck
 * @property        User                      $user
 * @method   static Builder<static>|Flashcard whereBackContext($value)
 * @method   static Builder<static>|Flashcard whereBackLang($value)
 * @method   static Builder<static>|Flashcard whereBackWord($value)
 * @method   static Builder<static>|Flashcard whereCreatedAt($value)
 * @method   static Builder<static>|Flashcard whereFlashcardDeckId($value)
 * @method   static Builder<static>|Flashcard whereFrontContext($value)
 * @method   static Builder<static>|Flashcard whereFrontLang($value)
 * @method   static Builder<static>|Flashcard whereFrontWord($value)
 * @method   static Builder<static>|Flashcard whereId($value)
 * @method   static Builder<static>|Flashcard whereLanguageLevel($value)
 * @method   static Builder<static>|Flashcard whereUpdatedAt($value)
 * @method   static Builder<static>|Flashcard whereUserId($value)
 * @property        null|string               $admin_id
 * @property        null|string               $emoji
 * @method   static Builder<static>|Flashcard whereAdminId($value)
 * @method   static Builder<static>|Flashcard whereEmoji($value)
 * @mixin \Eloquent
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
