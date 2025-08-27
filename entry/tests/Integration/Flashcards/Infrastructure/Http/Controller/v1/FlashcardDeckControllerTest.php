<?php

declare(strict_types=1);
use Tests\Base\FlashcardTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

test('index success', function () {
    // GIVEN
    $user = $this->createUser();
    $this->createFlashcardDeck(['user_id' => $user->id]);
    $this->createFlashcardDeck(['user_id' => $user->id]);

    // WHEN
    $response = $this->actingAs($user, 'sanctum')
        ->json('GET', route('flashcards.categories.index'), [
            'page' => 1,
            'per_page' => 15,
        ]);

    // THEN
    $response->assertStatus(200);
});
