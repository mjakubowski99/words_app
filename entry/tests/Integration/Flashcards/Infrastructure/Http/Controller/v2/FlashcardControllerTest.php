<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Infrastructure\Http\Controller\v2;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Rating;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\Language;
use App\Models\LearningSessionFlashcard;
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
            ->json('POST', route('v2.flashcards.store'), [
                'flashcard_deck_id' => $deck->id,
                'front_word' => 'Word',
                'front_context' => 'Context',
                'back_word' => 'Translation',
                'back_context' => 'Context translation',
                'language_level' => LanguageLevel::C1,
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
            'front_lang' => Language::pl()->getValue(),
            'back_lang' => Language::en()->getValue(),
            'language_level' => LanguageLevel::C1,
        ]);
    }

    public function test__Store_WhenUserNotAuthorized_fail(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create();
        // WHEN
        $response = $this->actingAs($user)
            ->json('POST', route('v2.flashcards.store'), [
                'flashcard_deck_id' => $deck->id,
                'front_word' => 'Word',
                'front_context' => 'Context',
                'back_word' => 'Translation',
                'back_context' => 'Context translation',
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
            ->json('PUT', route('v2.flashcards.update', ['flashcard_id' => $flashcard->id]), [
                'flashcard_deck_id' => $deck->id,
                'front_word' => 'Word',
                'front_context' => 'Context',
                'back_word' => 'Translation',
                'back_context' => 'Context translation',
                'language_level' => LanguageLevel::C2,
                'emoji' => '😀',
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
            'front_lang' => Language::pl()->getValue(),
            'back_lang' => Language::en()->getValue(),
            'language_level' => LanguageLevel::C2,
            'emoji' => json_encode('😀'),
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
            ->json('PUT', route('v2.flashcards.update', ['flashcard_id' => $flashcard->id]), [
                'flashcard_deck_id' => $deck->id,
                'front_word' => 'Word',
                'back_word' => 'Translation',
                'front_context' => 'Context',
                'back_context' => 'Context translation',
            ]);

        // THEN
        $response->assertStatus(403);
    }

    public function test__getByUser_WhenUserAuthorized_success(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $flashcard = Flashcard::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('GET', route('v2.flashcards.get.by-user'));

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'flashcards' => [
                    '*' => [
                        'id',
                        'front_word',
                        'front_lang',
                        'back_word',
                        'back_lang',
                        'front_context',
                        'back_context',
                        'rating',
                        'language_level',
                        'rating_percentage',
                    ],
                ],
                'page',
                'per_page',
                'flashcards_count',
            ],
        ]);
        $this->assertSame($flashcard['id'], $response->json('data.flashcards.*.id')[0]);
        $this->assertSame(0, $response->json('data.flashcards.*.rating_percentage')[0]);
    }

    public function test__userRatingStats_WhenUserAuthorized_success(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $flashcard = Flashcard::factory()->create([
            'user_id' => $user->id,
        ]);
        $flashcard = LearningSessionFlashcard::factory()->create([
            'flashcard_id' => $flashcard->id,
            'rating' => Rating::WEAK,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('GET', route('v2.flashcards.get.by-user.rating-stats'), [
                'owner_type' => FlashcardOwnerType::USER->value,
            ]);

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'rating',
                    'rating_percentage',
                ],
            ],
        ]);
    }

    public function test__bulkDelete_WhenUserAuthorized_success(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $flashcard_ids = [
            Flashcard::factory()->byUser($user)->create()->id,
            Flashcard::factory()->byUser($user)->create()->id,
        ];

        // WHEN
        $response = $this->actingAs($user)
            ->json('DELETE', route('v2.flashcards.bulk-delete'), [
                'flashcard_ids' => $flashcard_ids,
            ]);

        // THEN
        $response->assertStatus(204);
        foreach ($flashcard_ids as $flashcard_id) {
            $this->assertDatabaseMissing('flashcards', [
                'id' => $flashcard_id,
            ]);
        }
    }

    public function test__bulkDelete_WhenUserNotAuthorized_unauthorized(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $flashcard_ids = [
            Flashcard::factory()->byUser($user)->create()->id,
            Flashcard::factory()->byUser($user)->create()->id,
        ];

        // WHEN
        $response = $this
            ->json('DELETE', route('v2.flashcards.bulk-delete'), [
                'flashcard_ids' => $flashcard_ids,
            ]);

        // THEN
        $response->assertStatus(401);
    }
}
