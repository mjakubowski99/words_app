<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $story_id
 * @property int $flashcard_id
 * @property string|null $sentence_override
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Flashcard $flashcard
 * @method static \Database\Factories\StoryFlashcardFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereFlashcardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereSentenceOverride($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereStoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoryFlashcard extends Model
{
    use HasFactory;

    protected $table = 'story_flashcards';

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }
}
