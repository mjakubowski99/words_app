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
 * 
 *
 * @property User          $user
 * @property FlashcardDeck $deck
 * @property int $id
 * @property string|null $user_id
 * @property int|null $flashcard_deck_id
 * @property string $front_word
 * @property string $front_lang
 * @property string $back_word
 * @property string $back_lang
 * @property string $front_context
 * @property string $back_context
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $language_level
 * @property string|null $admin_id
 * @property string|null $emoji
 * @method static \Database\Factories\FlashcardFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereBackContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereBackLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereBackWord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereEmoji($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereFlashcardDeckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereFrontContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereFrontLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereFrontWord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereLanguageLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Flashcard whereUserId($value)
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
