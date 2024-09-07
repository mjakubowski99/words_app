<?php

namespace Flashcard\Domain\Services\AiGenerators;

use Flashcard\Domain\Models\AiFlashcard;
use Flashcard\Domain\Models\FlashcardPrompt;
use Shared\Integrations\Gemini\IGeminiApiClient;

class GeminiGenerator
{
    public function __construct(
        private IGeminiApiClient $client
    ) {}

    /** @return AiFlashcard[] */
    public function generate(FlashcardPrompt $prompt): array
    {
        $response = $this->client->generateText($prompt->get());

        if (!$response->success()) {
            throw new \Exception("Response error");
        }

        return $this->parseCsv($response->getGeneratedText());
    }

    private function parseCsv(string $text): array
    {
        $data = str_getcsv($text);

        $flashcards = [];

        foreach ($data as $row) {
            $flashcards[] = new AiFlashcard($row[0], $row[1], $row[2], $row[3]);
        }

        return $flashcards;
    }

    private function getCsvPrompt(): string
    {
        return file_get_contents(resource_path('prompts/generate_csv_flashcards_prompt.txt'));
    }
}