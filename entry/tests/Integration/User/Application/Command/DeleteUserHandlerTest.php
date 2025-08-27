<?php

declare(strict_types=1);
use App\Models\Report;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\SmTwoFlashcard;
use App\Models\LearningSession;
use App\Models\LearningSessionFlashcard;
use User\Application\Command\DeleteUserHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->handler = $this->app->make(DeleteUserHandler::class);
});
test('handle should delete user with all data', function () {
    // GIVEN
    $user = $this->createUser();
    SmTwoFlashcard::factory()->create(['user_id' => $user->id]);
    $deck = FlashcardDeck::factory()->create(['user_id' => $user->id]);
    $flashcard = Flashcard::factory()->create(['user_id' => $user->id]);
    $other_flashcard = Flashcard::factory()->create(['user_id' => $user->id]);
    $learning_session = LearningSession::factory()->create(['user_id' => $user->id]);
    $learning_session_flashcard = LearningSessionFlashcard::factory()->create([
        'flashcard_id' => $flashcard->id,
    ]);
    $ticket = Report::factory()->create([
        'user_id' => $user->id,
    ]);

    // WHEN
    $result = $this->handler->delete($user->getId());

    // THEN
    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
    $this->assertDatabaseMissing('flashcard_decks', [
        'id' => $deck->id,
    ]);
    $this->assertDatabaseMissing('flashcards', [
        'id' => $flashcard->id,
    ]);
    $this->assertDatabaseMissing('flashcards', [
        'id' => $other_flashcard->id,
    ]);
    $this->assertDatabaseMissing('learning_sessions', [
        'id' => $learning_session->id,
    ]);
    $this->assertDatabaseMissing('learning_session_flashcards', [
        'id' => $learning_session_flashcard->id,
    ]);
    $this->assertDatabaseHas('reports', [
        'id' => $ticket->id,
        'user_id' => null,
    ]);
});
