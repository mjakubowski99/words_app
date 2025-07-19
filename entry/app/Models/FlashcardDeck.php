<?php

declare(strict_types=1);

namespace App\Models;

use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Shared\Utils\ValueObjects\UserId;
use Illuminate\Database\Eloquent\Model;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property User $user
 * @property int $id
 * @property string $tag
 * @property string $name
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $default_language_level
 * @property string|null $admin_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Flashcard> $flashcards
 * @property-read int|null $flashcards_count
 * @method static \Database\Factories\FlashcardDeckFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck whereDefaultLanguageLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeck whereUserId($value)
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

    public function getUserId(): UserId
    {
        return new UserId($this->user_id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
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
