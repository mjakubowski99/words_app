<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $user_id
 * @property int $flashcard_id
 * @property int $easy_ratings_count
 * @property int $easy_ratings_count_to_purge
 * @property int $leitner_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Flashcard $flashcard
 * @method static \Database\Factories\FlashcardPollItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem whereEasyRatingsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem whereEasyRatingsCountToPurge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem whereFlashcardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem whereLeitnerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FlashcardPollItem whereUserId($value)
 * @mixin \Eloquent
 */
class FlashcardPollItem extends Model
{
    use HasFactory;

    protected $table = 'flashcard_poll_items';

    protected $guarded = [];

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class, 'flashcard_id');
    }
}
