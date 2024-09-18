<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\LearningSessionFlashcardFactory;

/**
 * @property        int                              $id
 * @property        int                              $learning_session_id
 * @property        int                              $flashcard_id
 * @property        null|int                         $rating
 * @property        null|Carbon                      $created_at
 * @property        null|Carbon                      $updated_at
 * @method   static LearningSessionFlashcardFactory  factory($count = null, $state = [])
 * @method   static Builder|LearningSessionFlashcard newModelQuery()
 * @method   static Builder|LearningSessionFlashcard newQuery()
 * @method   static Builder|LearningSessionFlashcard query()
 * @method   static Builder|LearningSessionFlashcard whereCreatedAt($value)
 * @method   static Builder|LearningSessionFlashcard whereFlashcardId($value)
 * @method   static Builder|LearningSessionFlashcard whereId($value)
 * @method   static Builder|LearningSessionFlashcard whereLearningSessionId($value)
 * @method   static Builder|LearningSessionFlashcard whereRating($value)
 * @method   static Builder|LearningSessionFlashcard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LearningSessionFlashcard extends Model
{
    use HasFactory;

    public function getId(): SessionFlashcardId
    {
        return new SessionFlashcardId($this->id);
    }

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }
}
