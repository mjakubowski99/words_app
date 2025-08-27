<?php

declare(strict_types=1);

use Tests\Base\FlashcardTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardRepository::class);
});
test('delete should delete flashcard', function () {
    // GIVEN
    $flashcard = $this->createFlashcard();
    $this->createSmTwoFlashcard(['flashcard_id' => $flashcard->id]);
    $this->createLearningSessionFlashcard(['flashcard_id' => $flashcard->id]);

    // WHEN
    $this->repository->delete($flashcard->getId());

    // THEN
    $this->assertDatabaseMissing('flashcards', [
        'id' => $flashcard->id,
    ]);
    $this->assertDatabaseMissing('sm_two_flashcards', [
        'flashcard_id' => $flashcard->id,
    ]);
    $this->assertDatabaseMissing('learning_session_flashcards', [
        'flashcard_id' => $flashcard->id,
    ]);
});
