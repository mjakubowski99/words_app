<?php

declare(strict_types=1);

namespace Tests\Smoke\Exercise\Infrastructure\Http\Controllers\ExerciseController;

use Tests\TestCase;
use App\Models\Exercise;
use App\Models\ExerciseEntry;
use App\Models\UnscrambleWordExercise;
use Exercise\Domain\Models\ExerciseStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExerciseControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test__answerUnscrambleWordExercise_WhenExerciseExistsAndValidAnswer_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = Exercise::factory()->create(['user_id' => $user->id]);
        $u_exercise = UnscrambleWordExercise::factory()->create(['exercise_id' => $exercise->id]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id]);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.unscramble-words.answer', [
            'exercise_entry_id' => $entry->id,
        ]), ['answer' => $u_exercise->word]);

        // THEN
        $response->assertStatus(204);
    }

    public function test__answerUnscrambleWordExercise_WhenExerciseExistsAndInvalidAnswer_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = Exercise::factory()->create(['user_id' => $user->id]);
        $u_exercise = UnscrambleWordExercise::factory()->create([
            'exercise_id' => $exercise->id,
            'word' => 'trxll',
        ]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id]);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.unscramble-words.answer', [
            'exercise_entry_id' => $entry->id,
        ]), ['answer' => 'tralalal']);

        // THEN
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'data' => [
                'assessment' => [
                    '*' => [
                        'character',
                        'correct',
                    ],
                ],
                'user_answer',
                'correct_answer',
            ],
        ]);

        // comaprsion trxll vs tralalal
        $this->assertSame(true, $response->json('data.assessment.0.correct'));
        $this->assertSame(true, $response->json('data.assessment.1.correct'));
        $this->assertSame(false, $response->json('data.assessment.2.correct'));
        $this->assertSame(true, $response->json('data.assessment.3.correct'));
        $this->assertSame(false, $response->json('data.assessment.4.correct'));
        $this->assertSame(false, $response->json('data.assessment.5.correct'));
        $this->assertSame(false, $response->json('data.assessment.6.correct'));
        $this->assertSame(false, $response->json('data.assessment.7.correct'));
    }

    public function test__skipUnscrambleWordExercise_WhenExerciseExists_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = Exercise::factory()->create(['user_id' => $user->id]);
        $u_exercise = UnscrambleWordExercise::factory()->create(['exercise_id' => $exercise->id]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id]);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.unscramble-words.skip', [
            'exercise_id' => $exercise->id,
        ]));

        // THEN
        $response->assertStatus(204);
    }

    public function test__skipUnscrambleWordExercise_WhenExerciseAlreadyDone_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = Exercise::factory()->create(['user_id' => $user->id, 'status' => ExerciseStatus::DONE]);
        $u_exercise = UnscrambleWordExercise::factory()->create(['exercise_id' => $exercise->id]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id]);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.unscramble-words.skip', [
            'exercise_id' => $exercise->id,
        ]));

        // THEN
        $response->assertStatus(403);
    }
}
