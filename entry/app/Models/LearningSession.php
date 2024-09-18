<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Flashcard\Domain\ValueObjects\SessionId;
use Database\Factories\LearningSessionFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                     $id
 * @property        string                  $user_id
 * @property        string                  $status
 * @property        string                  $device
 * @property        int                     $cards_per_session
 * @property        int                     $flashcard_category_id
 * @property        null|Carbon             $created_at
 * @property        null|Carbon             $updated_at
 * @property        FlashcardCategory       $category
 * @method   static LearningSessionFactory  factory($count = null, $state = [])
 * @method   static Builder|LearningSession newModelQuery()
 * @method   static Builder|LearningSession newQuery()
 * @method   static Builder|LearningSession query()
 * @method   static Builder|LearningSession whereCardsPerSession($value)
 * @method   static Builder|LearningSession whereCreatedAt($value)
 * @method   static Builder|LearningSession whereDevice($value)
 * @method   static Builder|LearningSession whereFlashcardCategoryId($value)
 * @method   static Builder|LearningSession whereId($value)
 * @method   static Builder|LearningSession whereStatus($value)
 * @method   static Builder|LearningSession whereUpdatedAt($value)
 * @method   static Builder|LearningSession whereUserId($value)
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(FlashcardCategory::class, 'flashcard_category_id');
    }

    public function toDomainModel(): Session
    {
        return (new Session(
            SessionStatus::from($this->status),
            $this->user->toOwner(),
            $this->cards_per_session,
            $this->device,
            $this->category()->first()->toDomainModel(),
        ))->init(new SessionId($this->id));
    }
}
