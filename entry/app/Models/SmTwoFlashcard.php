<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        User                                                         $user
 * @property        string                                                       $user_id
 * @property        int                                                          $flashcard_id
 * @property        string                                                       $repetition_ratio
 * @property        string                                                       $repetition_interval
 * @property        int                                                          $repetition_count
 * @property        null|Carbon                                                  $created_at
 * @property        null|Carbon                                                  $updated_at
 * @property        int                                                          $min_rating
 * @property        int                                                          $repetitions_in_session
 * @property        null|int                                                     $last_rating
 * @property        Flashcard                                                    $flashcard
 * @method   static \Database\Factories\SmTwoFlashcardFactory                    factory($count = null, $state = [])
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard newModelQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard newQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard query()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereCreatedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereFlashcardId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereLastRating($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereMinRating($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereRepetitionCount($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereRepetitionInterval($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereRepetitionRatio($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereRepetitionsInSession($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereUpdatedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|SmTwoFlashcard whereUserId($value)
 * @mixin \Eloquent
 */
class SmTwoFlashcard extends Model
{
    use HasFactory;

    public $incrementing = false;

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFlashcardId(): FlashcardId
    {
        return new FlashcardId($this->flashcard_id);
    }

    public function toDomainModel(): \Flashcard\Domain\Models\SmTwoFlashcard
    {
        return new \Flashcard\Domain\Models\SmTwoFlashcard(
            $this->user->getId(),
            $this->getFlashcardId(),
            (float) $this->repetition_ratio,
            (float) $this->repetition_interval,
            $this->repetition_count,
            $this->min_rating,
            $this->repetitions_in_session,
        );
    }
}
