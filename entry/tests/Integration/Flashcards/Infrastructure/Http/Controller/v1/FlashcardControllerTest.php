<?php

declare(strict_types=1);

namespace Integration\Flashcards\Infrastructure\Http\Controller\v1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Shared\Utils\ValueObjects\Language;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FlashcardControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test__Store_WhenUserAuthorized_StoreFlashcard(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('POST', route('flashcards.store'), [
                'flashcard_category_id' => $deck->id,
                'word' => 'Word',
                'translation' => 'Translation',
                'context' => 'Context',
                'context_translation' => 'Context translation',
            ]);

        // THEN
        $response->assertStatus(204);
        $this->assertDatabaseHas('flashcards', [
            'user_id' => $user->id,
            'flashcard_deck_id' => $deck->id,
            'front_word' => 'Word',
            'front_context' => 'Context',
            'back_word' => 'Translation',
            'back_context' => 'Context translation',
            'front_lang' => Language::pl(),
            'back_lang' => Language::en(),
        ]);
    }

    public function test__Store_WhenUserNotAuthorized_fail(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create();
        // WHEN
        $response = $this->actingAs($user)
            ->json('POST', route('flashcards.store'), [
                'flashcard_category_id' => $deck->id,
                'word' => 'Word',
                'translation' => 'Translation',
                'context' => 'Context',
                'context_translation' => 'Context translation',
            ]);

        // THEN
        $response->assertStatus(403);
    }

    public function test__Update_WhenUserAuthorized_UpdateFlashcard(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create([
            'user_id' => $user->id,
        ]);
        $flashcard = Flashcard::factory()->create([
            'user_id' => $user->id,
            'flashcard_deck_id' => $deck->id,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('PUT', route('flashcards.update', ['flashcard_id' => $flashcard->id]), [
                'flashcard_category_id' => $deck->id,
                'word' => 'Word',
                'translation' => 'Translation',
                'context' => 'Context',
                'context_translation' => 'Context translation',
            ]);

        // THEN
        $response->assertStatus(204);
        $this->assertDatabaseHas('flashcards', [
            'id' => $flashcard->id,
            'user_id' => $user->id,
            'flashcard_deck_id' => $deck->id,
            'front_word' => 'Word',
            'front_context' => 'Context',
            'back_word' => 'Translation',
            'back_context' => 'Context translation',
            'front_lang' => Language::pl(),
            'back_lang' => Language::en(),
        ]);
    }

    public function test__Update_WhenUserNotAuthorized_fail(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create();
        $flashcard = Flashcard::factory()->create([
            'flashcard_deck_id' => $deck->id,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('PUT', route('flashcards.update', ['flashcard_id' => $flashcard->id]), [
                'flashcard_category_id' => $deck->id,
                'word' => 'Word',
                'translation' => 'Translation',
                'context' => 'Context',
                'context_translation' => 'Context translation',
            ]);

        // THEN
        $response->assertStatus(403);
    }

    public function test__Delete_WhenUserAuthorized_DeleteFlashcard(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create([
            'user_id' => $user->id,
        ]);
        $flashcard = Flashcard::factory()->create([
            'user_id' => $user->id,
            'flashcard_deck_id' => $deck->id,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('DELETE', route('flashcards.delete', ['flashcard_id' => $flashcard->id]));

        // THEN
        $response->assertStatus(204);
        $this->assertDatabaseMissing('flashcards', [
            'id' => $flashcard->id,
        ]);
    }

    public function test__Delete_WhenUserNotAuthorized_fail(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $flashcard = Flashcard::factory()->create();

        // WHEN
        $response = $this->actingAs($user)
            ->json('DELETE', route('flashcards.delete', ['flashcard_id' => $flashcard->id]));

        // THEN
        $response->assertStatus(403);
    }
}
