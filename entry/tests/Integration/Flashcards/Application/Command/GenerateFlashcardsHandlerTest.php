<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use Tests\TestCase;
use App\Models\User;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Illuminate\Support\Facades\Http;
use Integrations\Gemini\GeminiApiClient;
use Flashcard\Application\Command\GenerateFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\GenerateFlashcardsHandler;
use Flashcard\Application\Exceptions\AiResponseFailedException;

class GenerateFlashcardsHandlerTest extends TestCase
{
    use DatabaseTransactions;

    private GenerateFlashcardsHandler $handler;

    private GeminiApiClient $client;

    private string $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->response = '{
               "candidates":[
                  {
                     "content":{
                        "parts":[
                           {
                              "text":"```json\n[\n  {\n    \"word\": \"kategoria\",\n    \"trans\": \"category\",\n    \"sentence\": \"Kategoria produkt\u00f3w spo\u017cywczych jest bardzo szeroka.\",\n    \"sentence_trans\": \"The category of food products is very wide.\"\n  },\n  {\n    \"word\": \"podkategoria\",\n    \"trans\": \"subcategory\",\n    \"sentence\": \"Podkategoria produkt\u00f3w mlecznych obejmuje jogurty i sery.\",\n    \"sentence_trans\": \"The subcategory of dairy products includes yogurt and cheese.\"\n  },\n  {\n    \"word\": \"klasyfikowa\u0107\",\n    \"trans\": \"classify\",\n    \"sentence\": \"Mo\u017cemy klasyfikowa\u0107 ksi\u0105\u017cki wed\u0142ug gatunku.\",\n    \"sentence_trans\": \"We can classify books by genre.\"\n  },\n  {\n    \"word\": \"klasyfikacja\",\n    \"trans\": \"classification\",\n    \"sentence\": \"Klasyfikacja zwierz\u0105t jest oparta na ich cechach.\",\n    \"sentence_trans\": \"Animal classification is based on their characteristics.\"\n  },\n  {\n    \"word\": \"rodzaj\",\n    \"trans\": \"type\",\n    \"sentence\": \"Istnieje wiele rodzaj\u00f3w kwiat\u00f3w.\",\n    \"sentence_trans\": \"There are many types of flowers.\"\n  },\n  {\n    \"word\": \"gatunek\",\n    \"trans\": \"genre\",\n    \"sentence\": \"M\u00f3j ulubiony gatunek muzyczny to rock.\",\n    \"sentence_trans\": \"My favorite genre of music is rock.\"\n  },\n  {\n    \"word\": \"grupa\",\n    \"trans\": \"group\",\n    \"sentence\": \"Grupa przyjaci\u00f3\u0142 posz\u0142a do kina.\",\n    \"sentence_trans\": \"A group of friends went to the cinema.\"\n  },\n  {\n    \"word\": \"zestaw\",\n    \"trans\": \"set\",\n    \"sentence\": \"Zestaw narz\u0119dzi jest niezb\u0119dny dla majsterkowicz\u00f3w.\",\n    \"sentence_trans\": \"A set of tools is essential for DIY enthusiasts.\"\n  },\n  {\n    \"word\": \"rodzina\",\n    \"trans\": \"family\",\n    \"sentence\": \"Rodzina ps\u00f3w obejmuje wiele ras.\",\n    \"sentence_trans\": \"The dog family includes many breeds.\"\n  },\n  {\n    \"word\": \"klasa\",\n    \"trans\": \"class\",\n    \"sentence\": \"Uczniowie w mojej klasie s\u0105 bardzo utalentowani.\",\n    \"sentence_trans\": \"The students in my class are very talented.\"\n  }\n]\n```"
                           }
                        ],
                        "role":"model"
                     },
                     "finishReason":"STOP",
                     "index":0,
                     "safetyRatings":[
                        {
                           "category":"HARM_CATEGORY_SEXUALLY_EXPLICIT",
                           "probability":"NEGLIGIBLE"
                        },
                        {
                           "category":"HARM_CATEGORY_HATE_SPEECH",
                           "probability":"NEGLIGIBLE"
                        },
                        {
                           "category":"HARM_CATEGORY_HARASSMENT",
                           "probability":"NEGLIGIBLE"
                        },
                        {
                           "category":"HARM_CATEGORY_DANGEROUS_CONTENT",
                           "probability":"NEGLIGIBLE"
                        }
                     ]
                  }
               ],
               "usageMetadata":{
                  "promptTokenCount":262,
                  "candidatesTokenCount":580,
                  "totalTokenCount":842
               }
            }';
        $this->handler = $this->app->make(GenerateFlashcardsHandler::class);
    }

    public function test__handle_ShouldGenerateFlashcards(): void
    {
        // GIVEN
        Http::fake([
            '*' => Http::response(json_decode($this->response, true)),
        ]);
        $deck_name = 'Category';
        $user = User::factory()->create();
        $command = new GenerateFlashcards(
            $user->toOwner(),
            $deck_name,
            LanguageLevel::A1
        );

        // WHEN
        $result = $this->handler->handle($command);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'name' => $deck_name,
            'user_id' => $command->getOwner()->getId(),
        ]);
        $this->assertDatabaseHas('flashcards', [
            'flashcard_deck_id' => $result->getDeckId(),
        ]);
        $this->assertFalse($result->getMergedToExistingDeck());
    }

    public function test__WhenCategoryAlreadyExists_ShouldGenerateFlashcardsAndAssignThemToExistingCategory(): void
    {
        // GIVEN
        Http::fake([
            '*' => Http::response(json_decode($this->response, true)),
        ]);
        $deck_name = 'Category';
        $user = User::factory()->create();
        FlashcardDeck::factory()->create(['name' => $deck_name, 'user_id' => $user->id]);
        $command = new GenerateFlashcards(
            $user->toOwner(),
            $deck_name,
            LanguageLevel::A1
        );

        // WHEN
        $result = $this->handler->handle($command);

        // THEN
        $this->assertDatabaseHas('flashcards', [
            'flashcard_deck_id' => $result->getDeckId(),
        ]);
        $this->assertTrue($result->getMergedToExistingDeck());
    }

    public function test__WhenCategoryAlreadyExistsAndApiFail_ShouldNotRemoveCategory(): void
    {
        // GIVEN
        Http::fake([
            '*' => Http::response(['message' => 'Error'], 400),
        ]);
        $deck_name = 'Category';
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create(['name' => $deck_name, 'user_id' => $user->id]);
        $command = new GenerateFlashcards(
            $user->toOwner(),
            $deck_name,
            LanguageLevel::A1
        );

        $this->expectException(AiResponseFailedException::class);

        // WHEN
        $this->handler->handle($command);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'id' => $deck->id,
        ]);
    }

    public function test__WhenNewCategoryAndApiFail_ShouldRemoveCategory(): void
    {
        // GIVEN
        Http::fake([
            '*' => Http::response(['message' => 'Error'], 400),
        ]);
        $deck_name = 'Category';
        $user = User::factory()->create();
        $command = new GenerateFlashcards(
            $user->toOwner(),
            $deck_name,
            LanguageLevel::A1
        );

        $this->expectException(AiResponseFailedException::class);

        // WHEN
        $this->handler->handle($command);

        // THEN
        $this->assertDatabaseMissing('flashcard_decks', [
            'user_id' => $user->id,
            'name' => $deck_name,
        ]);
    }
}
