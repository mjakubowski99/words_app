<?php

namespace Flashcard\Domain\Models;

class FlashcardPrompt
{
    private string $prompt;

    public function __construct(private readonly string $category)
    {
        $this->buildPrompt();
    }

    public function get(): string
    {
        return $this->prompt;
    }

    private function buildPrompt(): string
    {
        $this->readFromFile();
        $this->setCategory();
    }

    private function readFromFile(): void
    {
        $this->prompt = file_get_contents(resource_path('prompts/generate_csv_flashcards_prompt.txt'));
    }

    private function setCategory(): void
    {
        if (!str_contains($this->category, $this->prompt)) {
            throw new \Exception("Invalid prompt exception");
        }

        $this->prompt = str_replace('${{category}}', $this->category, $this->prompt);
    }

    private function removeWhiteCharacters(): void
    {
        $this->prompt = str_replace(["\n", "\r"], "", $this->prompt);
    }
}