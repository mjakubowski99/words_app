<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Exceptions\InvalidPromptException;

class FlashcardPrompt
{
    private readonly Language $word_lang;
    private readonly Language $translation_lang;

    private string $prompt = '
        Jesteś algorytmem ai generującym słowa do nauki angielskiego.
        Wygeneruj ${{words_count}} słów.
        Słowa muszą bezpośrednio nawiązywać do tematu narzuconego przez użytkownika. Przedstaw słowa po polsku jak i po angielsku.
        Dodaj takze emoji kontekstowe do slowka.
        Zapisz je w formie prostego kodu.
        Wzór:
        [{
        "word": "kasjer",
        "trans": "cashier",
        "sentence":"Kasjer przywitał mnie z uśmiechem.",
        "sentence_trans":"Cashier greeted me with a smile",
        "emoji":"😀"
        },...]
        Wygeneruj odpowiedź w formacie JSON zawierającą ${{words_count}} rekordów.
        Ton odpowiedzi: Jasne i zrozumiałe zdania, przydatne do praktycznej komunikacji w danej sytuacji.
        Uwzględnij również specyfikację poziomu języka. Wybrany poziom to: ${{level}}
        ${{letters_condition}}
        Zastosuj:
            - kreatywność w tworzeniu przykładów
            - losowe ziarno generowania: ${{seed}}
        Prompt użytkownika to: ${{category}}.
        Warunek błedu: Jeśli z jakiegoś powodu nie jesteś w stanie wygenerować rekordów dla danej sytuacji, zamiast rekordów odpowiedz w formacie {"error":"prompt"}
        Twoja odpowiedź ma zawierać tylko i wyłącznie dane w formacie JSON i nic więcej.
    ';

    public function __construct(
        private readonly string $category,
        private readonly LanguageLevel $language_level,
        private readonly int $words_count = 10,
        private array $initial_letters_to_avoid = [],
    ) {
        $this->buildPrompt();
        $this->word_lang = Language::pl();
        $this->translation_lang = Language::en();
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
        $this->setRandomSeed();
        $this->setCategory();
        $this->setLanguageLevel();
        $this->setWordsCount();
        $this->setInitialLettersToAvoid();
        $this->removeWhiteCharacters();
    }

    private function setRandomSeed(): void
    {
        str_replace('${{seed}}', (string) random_int(0, 1000), $this->prompt);
    }

    private function setCategory(): void
    {
        if (!str_contains($this->prompt, '${{category}}')) {
            throw new InvalidPromptException('Invalid prompt exception');
        }

        $this->prompt = str_replace('${{category}}', $this->category, $this->prompt);
    }

    private function setLanguageLevel(): void
    {
        if (!str_contains($this->prompt, '${{level}}')) {
            throw new InvalidPromptException('Invalid prompt exception. Language level not defined');
        }

        $this->prompt = str_replace('${{level}}', $this->language_level->value, $this->prompt);
    }

    private function setWordsCount(): void
    {
        if (!str_contains($this->prompt, '${{words_count}}')) {
            throw new InvalidPromptException('Invalid prompt exception');
        }

        $this->prompt = str_replace('${{words_count}}', (string) $this->words_count, $this->prompt);
    }

    private function setInitialLettersToAvoid(): void
    {
        if (!str_contains($this->prompt, '${{letters_condition}}')) {
            throw new InvalidPromptException('Invalid prompt exception');
        }

        $letters = implode(',', $this->initial_letters_to_avoid);

        if ($letters === '') {
            $this->prompt = str_replace('${{letters_condition}}', '', $this->prompt);
        } else {
            $condition = 'Unikaj słów zaczynających się na litery: ';
            $this->prompt = str_replace('${{letters_condition}}', $condition . $letters, $this->prompt);
        }
    }

    private function removeWhiteCharacters(): void
    {
        $this->prompt = str_replace(["\n", "\r"], '', $this->prompt);
    }
}
