<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property int $exercise_type
 * @property string $status
 * @property string $user_id
 * @property string|null $properties
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExerciseEntry> $entries
 * @property-read int|null $entries_count
 * @method static \Database\Factories\ExerciseFactory factory($count = null, $state = [])
 * @method static Builder<static>|Exercise newModelQuery()
 * @method static Builder<static>|Exercise newQuery()
 * @method static Builder<static>|Exercise query()
 * @method static Builder<static>|Exercise whereExerciseType($value)
 * @method static Builder<static>|Exercise whereId($value)
 * @method static Builder<static>|Exercise whereProperties($value)
 * @method static Builder<static>|Exercise whereStatus($value)
 * @method static Builder<static>|Exercise whereUserId($value)
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
