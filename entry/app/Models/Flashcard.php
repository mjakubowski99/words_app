<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Shared\Utils\ValueObjects\Language;
use Database\Factories\FlashcardFactory;
use Illuminate\Database\Eloquent\Builder;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int               $id
 * @property        string            $user_id
 * @property        int               $flashcard_category_id
 * @property        string            $front_word
 * @property        string            $front_lang
 * @property        string            $back_word
 * @property        string            $back_lang
 * @property        string            $front_context
 * @property        string            $back_context
 * @property        null|Carbon       $created_at
 * @property        null|Carbon       $updated_at
 * @method   static FlashcardFactory  factory($count = null, $state = [])
 * @method   static Builder|Flashcard newModelQuery()
 * @method   static Builder|Flashcard newQuery()
 * @method   static Builder|Flashcard query()
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
            $this->category->toDomainModel(),
        );
    }
}
