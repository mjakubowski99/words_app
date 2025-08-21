<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\AiGenerators;

use Shared\Models\Emoji;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Story;
use Illuminate\Support\Facades\Log;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\StoryId;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Integrations\Gemini\IGeminiApiClient;
use Flashcard\Application\Exceptions\AiResponseFailedException;
use Flashcard\Application\Exceptions\AiResponseProcessingFailException;

class GeminiGenerator implements IFlashcardGenerator
{
    public function __construct(
        private IGeminiApiClient $client
    ) {}

    public function generate(Owner $owner, Deck $deck, FlashcardPrompt $prompt): StoryCollection
    {
        $response = $this->client->generateText($prompt->getPrompt());

        if (!$response->success()) {
            $response = json_encode($response->getErrorResponse());

            Log::error('Generating flashcard decks failed', [
                'message' => is_string($response) ? $response : '',
                'owner_id' => $owner->getId(),
                'deck_name' => $deck->getName(),
            ]);

            throw new AiResponseFailedException(is_string($response) ? $response : '');
        }

        $text = $response->getGeneratedText();

        $stories = [];

        foreach ($this->parseChatResponse($text) as $row) {
            $story_index = $row['story_id'] ?? 0;

            if (!array_key_exists($story_index, $stories)) {
                $stories[$story_index] = [];
            }

            $stories[$story_index][] = new StoryFlashcard(
                StoryId::noId(),
                $story_index,
                null,
                new Flashcard(
                    FlashcardId::noId(),
                    (string) $row['word'],
                    $prompt->getWordLang(),
                    (string) $row['trans'],
                    $prompt->getTranslationLang(),
                    (string) $row['sentence'],
                    (string) $row['sentence_trans'],
                    $owner,
                    $deck,
                    $deck->getDefaultLanguageLevel(),
                    array_key_exists('emoji', $row) ? new Emoji($row['emoji']) : null,
                ),
            );
        }

        $results = [];
        foreach ($stories as $key => $flashcards) {
            $results[$key] = new Story(
                StoryId::noId(),
                $flashcards
            );
        }

        return new StoryCollection($results);
    }

    private function parseChatResponse(string $text): array
    {
        $pattern = '/```json(.*?)```/s';
        preg_match($pattern, $text, $matches);

        if (empty($matches)) {
            $rows = json_decode($text, true);

            if ($rows) {
                return $rows;
            }

            throw new AiResponseProcessingFailException();
        }

        $json = $matches[1];
        $json = mb_trim($json);
        $rows = json_decode($json, true);

        if (!$rows) {
            throw new AiResponseProcessingFailException();
        }

        return $rows;
    }
}
