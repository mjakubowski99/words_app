<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FlashcardFactory;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Shared\Utils\ValueObjects\Language;

/**
 * @property        int               $id
 * @property        string            $user_id
 * @property        int               $flashcard_category_id
 * @property        string            $word
 * @property        string            $word_lang
 * @property        string            $translation
 * @property        string            $translation_lang
 * @property        string            $context
 * @property        string            $context_translation
 * @property        null|Carbon       $created_at
 * @property        null|Carbon       $updated_at
 * @method   static FlashcardFactory  factory($count = null, $state = [])
 * @method   static Builder|Flashcard newModelQuery()
 * @method   static Builder|Flashcard newQuery()
 * @method   static Builder|Flashcard query()
 * @method   static Builder|Flashcard whereContext($value)
 * @method   static Builder|Flashcard whereContextTranslation($value)
 * @method   static Builder|Flashcard whereCreatedAt($value)
 * @method   static Builder|Flashcard whereFlashcardCategoryId($value)
 * @method   static Builder|Flashcard whereId($value)
 * @method   static Builder|Flashcard whereTranslation($value)
 * @method   static Builder|Flashcard whereTranslationLang($value)
 * @method   static Builder|Flashcard whereUpdatedAt($value)
 * @method   static Builder|Flashcard whereUserId($value)
 * @method   static Builder|Flashcard whereWord($value)
 * @method   static Builder|Flashcard whereWordLang($value)
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(FlashcardCategory::class, 'flashcard_category_id');
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
            $this->user->toOwner(),
            $this->category->toDomainModel(),
        );
    }
}
