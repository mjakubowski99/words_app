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
 * @property string $correct_answer
 * @property string|null $last_answer
 * @property bool|null $last_answer_correct
 * @property float $score
 * @property int $answers_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $order
 * @method static \Database\Factories\ExerciseEntryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereAnswersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereCorrectAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereLastAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereLastAnswerCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExerciseEntry extends Model
{
    use HasFactory;
}
