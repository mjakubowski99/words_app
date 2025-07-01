<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\AiGenerators;

use Flashcard\Domain\Models\Story;
use Flashcard\Domain\Models\StoryFlashcard;
use Shared\Models\Emoji;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\Log;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Integrations\Gemini\IGeminiApiClient;
use Flashcard\Application\Exceptions\AiResponseFailedException;
use Flashcard\Application\Exceptions\AiResponseProcessingFailException;
use Shared\Utils\ValueObjects\StoryId;

class GeminiGenerator implements IFlashcardGenerator
{
    public function __construct(
        private IGeminiApiClient $client
    ) {}

    public function generate(Owner $owner, Deck $deck, FlashcardPrompt $prompt): array
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
            $story_id = $row['story_id'] ?? 0;

            if (!array_key_exists($story_id, $stories)) {
                $stories[$story_id] = [];
            }

            $stories[$story_id][] = new StoryFlashcard(
                StoryId::noId(),
                $story_id,
                (string) $row['sentence_trans'],
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

        return $results;
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
        $json = trim($json);
        $rows = json_decode($json, true);

        if (!$rows) {
            throw new AiResponseProcessingFailException();
        }

        return $rows;
    }
}
