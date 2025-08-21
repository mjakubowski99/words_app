<?php

declare(strict_types=1);

namespace Tests\Integration\Exercise\Infrastructure\Http\Controllers\ExerciseController;

use Tests\TestCase;
use App\Models\Exercise;
use App\Models\ExerciseEntry;
use App\Models\UnscrambleWordExercise;
use Exercise\Domain\Models\ExerciseStatus;
use Database\Factories\WordMatchExerciseFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExerciseControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test__answerUnscrambleWordExercise_WhenExerciseExistsAndValidAnswer_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = Exercise::factory()->create(['user_id' => $user->id]);
        $u_exercise = UnscrambleWordExercise::factory()->create([
            'exercise_id' => $exercise->id,
            'word' => 'applepie',
        ]);
        $entry = ExerciseEntry::factory()->create(['exercise_id' => $exercise->id]);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.unscramble-words.answer', [
            'exercise_entry_id' => $entry->id,
        ]), [
            'answer' => $u_exercise->word,
            'hints_count' => 2,
        ]);

        // THEN
        $response->assertStatus(204);
        $this->assertDatabaseHas('exercise_entries', [
            'id' => $entry->id,
            'score' => 75.0,
        ]);
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
        ]), [
            'answer' => 'tralalal',
        ]);

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

    public function test__answerWordMatchExercise_WhenExerciseExistsAndValidAnswer_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = WordMatchExerciseFactory::createNew([
            'user_id' => $user->id,
            'status' => ExerciseStatus::NEW,
        ], 2, false);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.word-match.answer', [
            'exercise_id' => $exercise->id,
        ]), [
            'answers' => [
                [
                    'exercise_entry_id' => $exercise->entries[0]->id,
                    'answer' => $exercise->entries[0]->correct_answer,
                ],
            ],
        ]);

        // THEN
        $response->assertOk();
        $this->assertTrue($response->json('data.assessments.0.is_correct'));
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'status' => ExerciseStatus::IN_PROGRESS,
        ]);
    }

    public function test__answerWordMatchExercise_WhenOneValidAnswerAndOneInvalid_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = WordMatchExerciseFactory::createNew([
            'user_id' => $user->id,
            'status' => ExerciseStatus::NEW,
        ], 2, false);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.word-match.answer', [
            'exercise_id' => $exercise->id,
        ]), [
            'answers' => [
                [
                    'exercise_entry_id' => $exercise->entries[0]->id,
                    'answer' => $exercise->entries[0]->correct_answer,
                ],
                [
                    'exercise_entry_id' => $exercise->entries[1]->id,
                    'answer' => $exercise->entries[1]->correct_answer . 'invalid',
                ],
            ],
        ]);

        // THEN
        $response->assertOk();
        $this->assertTrue($response->json('data.assessments.0.is_correct'));
        $this->assertFalse($response->json('data.assessments.1.is_correct'));
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'status' => ExerciseStatus::IN_PROGRESS,
        ]);
    }

    public function test__answerWordMatchExercise_WhenTwoValidAnswers_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = WordMatchExerciseFactory::createNew([
            'user_id' => $user->id,
            'status' => ExerciseStatus::NEW,
        ], 2, false);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.word-match.answer', [
            'exercise_id' => $exercise->id,
        ]), [
            'answers' => [
                [
                    'exercise_entry_id' => $exercise->entries[0]->id,
                    'answer' => $exercise->entries[0]->correct_answer,
                ],
                [
                    'exercise_entry_id' => $exercise->entries[1]->id,
                    'answer' => $exercise->entries[1]->correct_answer,
                ],
            ],
        ]);

        // THEN
        $response->assertOk();
        $this->assertTrue($response->json('data.assessments.0.is_correct'));
        $this->assertTrue($response->json('data.assessments.1.is_correct'));
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'status' => ExerciseStatus::DONE,
        ]);
    }

    public function test__answerWordMatchExercise_WhenExerciseWithStory_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = WordMatchExerciseFactory::createNew([
            'user_id' => $user->id,
            'status' => ExerciseStatus::NEW,
        ], 1, true);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.word-match.answer', [
            'exercise_id' => $exercise->id,
        ]), [
            'answers' => [
                [
                    'exercise_entry_id' => $exercise->entries[0]->id,
                    'answer' => $exercise->entries[0]->correct_answer,
                ],
            ],
        ]);

        // THEN
        $response->assertOk();
        $this->assertTrue($response->json('data.assessments.0.is_correct'));
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'status' => ExerciseStatus::DONE,
        ]);
    }

    public function test__skipWordMatchExercise_skipExercise(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = WordMatchExerciseFactory::createNew([
            'user_id' => $user->id,
            'status' => ExerciseStatus::NEW,
        ], 2, false);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.word-match.skip', [
            'exercise_id' => $exercise->id,
        ]));

        // THEN
        $response->assertNoContent();
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'status' => ExerciseStatus::SKIPPED,
        ]);
    }

    public function test__skipWordMatchExercise_NotOwner_fail(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = WordMatchExerciseFactory::createNew([
            'status' => ExerciseStatus::NEW,
        ], 2, false);

        // WHEN
        $response = $this->actingAs($user)->putJson(route('v2.exercises.word-match.skip', [
            'exercise_id' => $exercise->id,
        ]));

        // THEN
        $response->assertStatus(401);
    }
}
