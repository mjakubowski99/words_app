<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Database\Factories\FlashcardDeckFactory;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                           $id
 * @property        string                        $tag
 * @property        string                        $name
 * @property        string                        $user_id
 * @property        null|Carbon                   $created_at
 * @property        null|Carbon                   $updated_at
 * @method   static FlashcardDeckFactory          factory($count = null, $state = [])
 * @method   static Builder|FlashcardDeck         newModelQuery()
 * @method   static Builder|FlashcardDeck         newQuery()
 * @method   static Builder|FlashcardDeck         query()
 * @property        string                        $default_language_level
 * @property        null|User                     $user
 * @method   static Builder<static>|FlashcardDeck whereCreatedAt($value)
 * @method   static Builder<static>|FlashcardDeck whereDefaultLanguageLevel($value)
 * @method   static Builder<static>|FlashcardDeck whereId($value)
 * @method   static Builder<static>|FlashcardDeck whereName($value)
 * @method   static Builder<static>|FlashcardDeck whereTag($value)
 * @method   static Builder<static>|FlashcardDeck whereUpdatedAt($value)
 * @method   static Builder<static>|FlashcardDeck whereUserId($value)
 * @mixin \Eloquent
 */
class FlashcardDeck extends Model
{
    use HasFactory;

    protected $table = 'flashcard_decks';

    public function getId(): FlashcardDeckId
    {
        return new FlashcardDeckId($this->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toDomainModel(): Deck
    {
        return (new Deck(
            $this->user->toOwner(),
            $this->tag,
            $this->name,
            LanguageLevel::from($this->default_language_level),
        ))->init(new FlashcardDeckId($this->id));
    }
}
