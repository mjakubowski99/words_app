<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                                                          $id
 * @property        int                                                          $story_id
 * @property        int                                                          $flashcard_id
 * @property        null|string                                                  $sentence_override
 * @property        null|Carbon                                                  $created_at
 * @property        null|Carbon                                                  $updated_at
 * @property        Flashcard                                                    $flashcard
 * @method   static \Database\Factories\StoryFlashcardFactory                    factory($count = null, $state = [])
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard newModelQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard newQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard query()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereCreatedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereFlashcardId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereSentenceOverride($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereStoryId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|StoryFlashcard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoryFlashcard extends Model
{
    use HasFactory;

    protected $table = 'story_flashcards';

    protected $guarded = [];

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }
}
