<?php

declare(strict_types=1);

use App\Models\Exercise;
use App\Models\ExerciseEntry;
use App\Models\UnscrambleWordExercise;
use Database\Factories\WordMatchExerciseFactory;
use Exercise\Domain\Models\ExerciseStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('answer unscramble word exercise when exercise exists and valid answer success', function () {
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
});

test('answer unscramble word exercise when exercise exists and invalid answer success', function () {
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
    expect($response->json('data.assessment.0.correct'))->toBe(true)
        ->and($response->json('data.assessment.1.correct'))->toBe(true)
        ->and($response->json('data.assessment.2.correct'))->toBe(false)
        ->and($response->json('data.assessment.3.correct'))->toBe(true)
        ->and($response->json('data.assessment.4.correct'))->toBe(false)
        ->and($response->json('data.assessment.5.correct'))->toBe(false)
        ->and($response->json('data.assessment.6.correct'))->toBe(false)
        ->and($response->json('data.assessment.7.correct'))->toBe(false);
});

test('skip unscramble word exercise when exercise exists success', function () {
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
});

test('skip unscramble word exercise when exercise already done success', function () {
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
});

test('answer word match exercise when exercise exists and valid answer success', function () {
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
    expect($response->json('data.assessments.0.is_correct'))->toBeTrue();
    $this->assertDatabaseHas('exercises', [
        'id' => $exercise->id,
        'status' => ExerciseStatus::IN_PROGRESS,
    ]);
});

test('answer word match exercise when one valid answer and one invalid success', function () {
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
    expect($response->json('data.assessments.0.is_correct'))->toBeTrue()
        ->and($response->json('data.assessments.1.is_correct'))->toBeFalse();
    $this->assertDatabaseHas('exercises', [
        'id' => $exercise->id,
        'status' => ExerciseStatus::IN_PROGRESS,
    ]);
});

test('answer word match exercise remove options', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = WordMatchExerciseFactory::createNew([
        'user_id' => $user->id,
        'status' => ExerciseStatus::NEW,
    ], 1, false);

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
    $exercise = Exercise::findOrFail($exercise->id);

    expect(json_decode($exercise->properties)->answer_options)
        ->toBeArray()
        ->toHaveCount(0);
});

test('answer word match exercise when two valid answers success', function () {
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
    expect($response->json('data.assessments.0.is_correct'))->toBeTrue()
        ->and($response->json('data.assessments.1.is_correct'))->toBeTrue();
    $this->assertDatabaseHas('exercises', [
        'id' => $exercise->id,
        'status' => ExerciseStatus::DONE,
    ]);
});

test('answer word match exercise when exercise with story success', function () {
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
    expect($response->json('data.assessments.0.is_correct'))->toBeTrue();
    $this->assertDatabaseHas('exercises', [
        'id' => $exercise->id,
        'status' => ExerciseStatus::DONE,
    ]);
});

test('skip word match exercise skip exercise', function () {
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
});

test('skip word match exercise not owner fail', function () {
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
});
