<?php

declare(strict_types=1);

namespace App\Models;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\UserId;

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
