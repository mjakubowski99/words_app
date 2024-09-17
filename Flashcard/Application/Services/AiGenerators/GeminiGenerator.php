<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\AiGenerators;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Integrations\Gemini\IGeminiApiClient;
use Flashcard\Application\Exceptions\GeminiApiException;

class GeminiGenerator implements IFlashcardGenerator
{
    public function __construct(
        private IGeminiApiClient $client
    ) {}

    public function generate(Owner $owner, Category $category, FlashcardPrompt $prompt): array
    {
        $response = $this->client->generateText($prompt->getPrompt());

        if (!$response->success()) {
            $response = json_encode($response->getErrorResponse());

            throw new GeminiApiException(is_string($response) ? $response : '');
        }

        $text = $response->getGeneratedText();

        $flashcards = [];

        foreach ($this->parseChatResponse($text) as $row) {
            $flashcards[] = new Flashcard(
                new FlashcardId(0),
                (string) $row['word'],
                $prompt->getWordLang(),
                (string) $row['trans'],
                $prompt->getTranslationLang(),
                (string) $row['sentence'],
                (string) $row['sentence_trans'],
                $owner,
                $category,
            );
        }

        return $flashcards;
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

            throw new \Exception('Failed to parse chat response');
        }

        $json = $matches[1];
        $json = trim($json);
        $rows = json_decode($json, true);

        return $rows;
    }
}
