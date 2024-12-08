<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Infrastructure\Http\Controller\v2;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Base\FlashcardTestCase;

class FlashcardDeckControllerTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    public function test__index_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $this->createFlashcardDeck(['user_id' => $user->id]);
        $this->createFlashcardDeck(['user_id' => $user->id]);

        // WHEN
        $response = $this->actingAs($user, 'sanctum')
            ->json('GET', route('v2.flashcards.decks.index'), [
                'page' => 1,
                'per_page' => 15,
            ]);

        // THEN
        $response->assertStatus(200);
    }
}
