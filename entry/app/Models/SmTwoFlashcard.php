<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Database\Factories\SmTwoFlashcardFactory;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        string                 $user_id
 * @property        int                    $flashcard_id
 * @property        string                 $repetition_ratio
 * @property        string                 $repetition_interval
 * @property        int                    $repetition_count
 * @property        null|Carbon            $created_at
 * @property        null|Carbon            $updated_at
 * @property        Flashcard              $flashcard
 * @method   static SmTwoFlashcardFactory  factory($count = null, $state = [])
 * @method   static Builder|SmTwoFlashcard newModelQuery()
 * @method   static Builder|SmTwoFlashcard newQuery()
 * @method   static Builder|SmTwoFlashcard query()
 * @method   static Builder|SmTwoFlashcard whereCreatedAt($value)
 * @method   static Builder|SmTwoFlashcard whereFlashcardId($value)
 * @method   static Builder|SmTwoFlashcard whereRepetitionCount($value)
 * @method   static Builder|SmTwoFlashcard whereRepetitionInterval($value)
 * @method   static Builder|SmTwoFlashcard whereRepetitionRatio($value)
 * @method   static Builder|SmTwoFlashcard whereUpdatedAt($value)
 * @method   static Builder|SmTwoFlashcard whereUserId($value)
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
        );
    }
}
