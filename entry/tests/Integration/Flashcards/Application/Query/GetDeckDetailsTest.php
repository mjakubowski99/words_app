<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Query;

use Shared\Enum\LanguageLevel;
use Tests\TestCase;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Flashcard\Application\Query\GetDeckDetails;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GetDeckDetailsTest extends TestCase
{
    use DatabaseTransactions;

    private GetDeckDetails $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->query = $this->app->make(GetDeckDetails::class);
    }

    public function test__handle_ShouldReturnCategory(): void
    {
        // GIVEN
        $deck = FlashcardDeck::factory()->create();
        $flashcard = Flashcard::factory()->create([
            'flashcard_deck_id' => $deck->id,
            'language_level' => LanguageLevel::C1->value,
        ]);

        // WHEN
        $result = $this->query->get($deck->getId(), null, 1, 15);

        // THEN
        $this->assertSame($deck->id, $result->getId()->getValue());
        $this->assertSame($deck->name, $result->getName());
        $this->assertSame(1, count($result->getFlashcards()));
        $this->assertSame($flashcard->language_level, $result->getFlashcards()[0]->getLanguageLevel()->value);
    }
}
