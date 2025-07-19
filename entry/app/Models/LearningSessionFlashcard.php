<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $learning_session_id
 * @property int $flashcard_id
 * @property int|null $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_additional
 * @property int|null $exercise_entry_id
 * @property int|null $exercise_type
 * @property-read \App\Models\Flashcard $flashcard
 * @property-read \App\Models\LearningSession $session
 * @method static \Database\Factories\LearningSessionFlashcardFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereExerciseEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereExerciseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereFlashcardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereIsAdditional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereLearningSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearningSessionFlashcard whereUpdatedAt($value)
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

    public function session(): BelongsTo
    {
        return $this->belongsTo(LearningSession::class, 'learning_session_id');
    }
}
