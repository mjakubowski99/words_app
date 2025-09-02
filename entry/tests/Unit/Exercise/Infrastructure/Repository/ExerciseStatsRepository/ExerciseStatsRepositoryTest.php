<?php

declare(strict_types=1);

use App\Models\ExerciseEntry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Infrastructure\Repositories\ExerciseStatsRepository;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(ExerciseStatsRepository::class);
});

test('getScoreSum should return correct sum of scores', function () {
    // GIVEN
    $entry1 = ExerciseEntry::factory()->create([
        'score' => 1.5,
    ]);
    $entry2 = ExerciseEntry::factory()->create([
        'score' => 2.5,
    ]);

    // WHEN
    $result = $this->repository->getScoreSum([
        $entry1->id,
        $entry2->id,
    ]);

    // THEN
    expect($result)->toBe(4.0);
});

test('getScoreSum when no scores return 0.0', function () {
    // GIVEN
    $entry1 = ExerciseEntry::factory()->create([
        'score' => 2.0,
    ]);

    // WHEN
    $result = $this->repository->getScoreSum([
        9999, // non-existing ID
    ]);

    // THEN
    expect($result)->toBe(0.0);
});
