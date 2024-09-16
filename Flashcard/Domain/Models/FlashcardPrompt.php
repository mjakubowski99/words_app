<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Exceptions\InvalidPromptException;

class FlashcardPrompt
{
    private string $prompt = '
        Jesteś algorytmem ai generującym słowa do nauki angielskiego.
        Bazujesz na krzywej zapominania i algorytmach typu Anki lub super memo.
        Wygeneruj 10 słów.
        Słowa muszą bezpośrednio nawiązywać do tematu narzuconego przez użytkownika. Przedstaw słowa po polsku jak i po angielsku.
        Zapisz je w formie prostego kodu.
        Wzór:
        [{
        "word": "kasjer",
        "trans": "cashier",
        "sentence":"Kasjer przywitał mnie z uśmiechem.",
        "sentence_trans":"Cashier greeted me with a smile"
        },...]
        Wygeneruj odpowiedź w formacie JSON zawierającą 10 rekordów.
        Ton odpowiedzi: Jasne i zrozumiałe zdania, przydatne do praktycznej komunikacji w danej sytuacji.
        Prompt użytkownika to: ${{category}}.
        Warunek błedu: Jeśli z jakiegoś powodu nie jesteś w stanie wygenerować rekordów dla danej sytuacji, zamiast rekordów odpowiedz w formacie {"error":"prompt"}
        Twoja odpowiedź ma zawierać tylko i wyłącznie dane w formacie JSON i nic więcej.
    ';

    public function __construct(
        private readonly string $category,
        private readonly Language $word_lang,
        private readonly Language $translation_lang,
    ) {
        $this->buildPrompt();
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getWordLang(): Language
    {
        return $this->word_lang;
    }

    public function getTranslationLang(): Language
    {
        return $this->translation_lang;
    }

    private function buildPrompt(): void
    {
        $this->setCategory();
        $this->removeWhiteCharacters();
    }

    private function setCategory(): void
    {
        if (!str_contains($this->prompt, '${{category}}')) {
            throw new InvalidPromptException('Invalid prompt exception');
        }

        $this->prompt = str_replace('${{category}}', $this->category, $this->prompt);
    }

    private function removeWhiteCharacters(): void
    {
        $this->prompt = str_replace(["\n", "\r"], '', $this->prompt);
    }
}
