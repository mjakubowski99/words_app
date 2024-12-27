<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Flashcard\Domain\Models\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Flashcard\Domain\ValueObjects\SessionId;
use Database\Factories\LearningSessionFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                             $id
 * @property        string                          $user_id
 * @property        string                          $status
 * @property        string                          $device
 * @property        int                             $cards_per_session
 * @property        int                             $flashcard_deck_id
 * @property        null|Carbon                     $created_at
 * @property        null|Carbon                     $updated_at
 * @property        FlashcardDeck                   $deck
 * @method   static LearningSessionFactory          factory($count = null, $state = [])
 * @method   static Builder|LearningSession         newModelQuery()
 * @method   static Builder|LearningSession         newQuery()
 * @method   static Builder|LearningSession         query()
 * @property        User                            $user
 * @method   static Builder<static>|LearningSession whereCardsPerSession($value)
 * @method   static Builder<static>|LearningSession whereCreatedAt($value)
 * @method   static Builder<static>|LearningSession whereDevice($value)
 * @method   static Builder<static>|LearningSession whereFlashcardDeckId($value)
 * @method   static Builder<static>|LearningSession whereId($value)
 * @method   static Builder<static>|LearningSession whereStatus($value)
 * @method   static Builder<static>|LearningSession whereUpdatedAt($value)
 * @method   static Builder<static>|LearningSession whereUserId($value)
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
