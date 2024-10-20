<?php

declare(strict_types=1);

namespace Integration\Flashcards\Infrastructure\Http\Controller;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardCategory;
use Shared\Utils\ValueObjects\Language;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FlashcardControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test__Store_WhenUserAuthorized_StoreFlashcard(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $category = FlashcardCategory::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('POST', route('flashcards.store'), [
                'flashcard_category_id' => $category->id,
                'word' => 'Word',
                'translation' => 'Translation',
                'context' => 'Context',
                'context_translation' => 'Context translation',
            ]);

        // THEN
        $response->assertStatus(204);
        $this->assertDatabaseHas('flashcards', [
            'user_id' => $user->id,
            'flashcard_category_id' => $category->id,
            'word' => 'Word',
            'context' => 'Context',
            'translation' => 'Translation',
            'context_translation' => 'Context translation',
            'word_lang' => Language::PL,
            'translation_lang' => Language::EN,
        ]);
    }

    public function test__Update_WhenUserAuthorized_UpdateFlashcard(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $category = FlashcardCategory::factory()->create([
            'user_id' => $user->id,
        ]);
        $flashcard = Flashcard::factory()->create([
            'user_id' => $user->id,
            'flashcard_category_id' => $category->id,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('PUT', route('flashcards.update', ['flashcard_id' => $flashcard->id]), [
                'flashcard_category_id' => $category->id,
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
            'flashcard_category_id' => $category->id,
            'word' => 'Word',
            'context' => 'Context',
            'translation' => 'Translation',
            'context_translation' => 'Context translation',
            'word_lang' => Language::PL,
            'translation_lang' => Language::EN,
        ]);
    }

    public function test__Delete_WhenUserAuthorized_DeleteFlashcard(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $category = FlashcardCategory::factory()->create([
            'user_id' => $user->id,
        ]);
        $flashcard = Flashcard::factory()->create([
            'user_id' => $user->id,
            'flashcard_category_id' => $category->id,
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
}
