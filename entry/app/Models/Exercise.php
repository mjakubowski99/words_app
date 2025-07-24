<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                                 $id
 * @property        int                                 $exercise_type
 * @property        string                              $status
 * @property        string                              $user_id
 * @property        null|string                         $properties
 * @property        Collection<int, ExerciseEntry>      $entries
 * @property        null|int                            $entries_count
 * @method   static \Database\Factories\ExerciseFactory factory($count = null, $state = [])
 * @method   static Builder<static>|Exercise            newModelQuery()
 * @method   static Builder<static>|Exercise            newQuery()
 * @method   static Builder<static>|Exercise            query()
 * @method   static Builder<static>|Exercise            whereExerciseType($value)
 * @method   static Builder<static>|Exercise            whereId($value)
 * @method   static Builder<static>|Exercise            whereProperties($value)
 * @method   static Builder<static>|Exercise            whereStatus($value)
 * @method   static Builder<static>|Exercise            whereUserId($value)
 * @mixin \Eloquent
 */
class Exercise extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function entries(): HasMany
    {
        return $this->hasMany(ExerciseEntry::class, 'exercise_id');
    }
}
