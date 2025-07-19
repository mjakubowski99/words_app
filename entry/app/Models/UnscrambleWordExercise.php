<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $exercise_id
 * @property string $word
 * @property string $scrambled_word
 * @property string $context_sentence
 * @property string $word_translation
 * @property string|null $emoji
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\UnscrambleWordExerciseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereContextSentence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereEmoji($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereScrambledWord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereWord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnscrambleWordExercise whereWordTranslation($value)
 * @mixin \Eloquent
 */
class UnscrambleWordExercise extends Model
{
    use HasFactory;
}
