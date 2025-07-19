<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Flashcard\Domain\ValueObjects\SessionId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $user_id
 * @property string $status
 * @property string $device
 * @property int $cards_per_session
 * @property int|null $flashcard_deck_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type
 * @property-read \App\Models\FlashcardDeck|null $deck
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\LearningSessionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereCardsPerSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereFlashcardDeckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSession whereUserId($value)
 * @mixin \Eloquent
 */
class LearningSession extends Model
{
    use HasFactory;

    public function getId(): SessionId
    {
        return new SessionId($this->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deck(): BelongsTo
    {
        return $this->belongsTo(FlashcardDeck::class, 'flashcard_deck_id');
    }
}
