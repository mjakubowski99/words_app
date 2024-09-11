<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services\Generators;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\Models\Owner;
use Shared\Integrations\Gemini\IGeminiApiClient;

class GeminiGenerator implements IFlashcardGenerator
{
    public function __construct(
        private IGeminiApiClient $client
    ) {}

    public function generate(Owner $owner, Category $category, FlashcardPrompt $prompt): array
    {
        $response = $this->client->generateText($prompt->getPrompt());

        if (!$response->success()) {
            throw new \Exception(json_encode($response->getErrorResponse()));
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
                $category->getId()
            );
        }

        return $flashcards;
    }

    private function parseChatResponse(string $text): array
    {
        $pattern = '/```json(.*?)```/s';
        preg_match($pattern, $text, $matches);

        if (!empty($matches)) {
            $json = $matches[1];
            $json = trim($json);
            $rows = json_decode($json, true);

            if (!$rows) {
                $rows = json_decode($text, true);
            }
        }
        return $rows;
    }
}