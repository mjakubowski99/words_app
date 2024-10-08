<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Infrastructure\Http\Controller;

use App\Models\User;
use App\Models\FlashcardCategory;
use Tests\Base\FlashcardTestCase;

class FlashcardCategoryControllerTest extends FlashcardTestCase
{
    public function test__index_success(): void
    {
        // GIVEN
        $user = User::factory()->create();
        FlashcardCategory::factory(2)->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $response = $this->actingAs($user, 'sanctum')
            ->json('GET', route('flashcards.categories.index'), [
                'page' => 1,
                'per_page' => 15,
            ]);

        // THEN
        $response->assertStatus(200);
    }
}
