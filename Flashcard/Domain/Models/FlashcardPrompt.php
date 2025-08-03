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
        Jesteś algorytmem AI generującym słowa do nauki języka angielskiego.
        Na podstawie tematu podanego przez użytkownika utwórz historię składającą się łącznie z ${{words_count}} zdań po angielsku. 
        Podziel historię na części, które muszą mieć po 3–4 zdania każda — każda część to osobna mini-historia, 
        która w obrębie tych zdań musi tworzyć spójną, logiczną całość (czyli krótkie zdarzenie z początkiem, środkiem i końcem).
        Dla każdego zdania wygeneruj jego tłumaczenie na język polski.
        Następnie z wygenerowanych zdań wyodrębnij słowa do fiszek (flashcards). Wybrane słowa muszą:
        – występować w zdaniu w swojej podstawowej (nieodmienionej) formie,
        – bezpośrednio odnosić się do tematu,
        – nie powtarzać się w innych historiach.
        Story: rozmowa z kasjerem
            - Emma walked into the store and picked up a bottle of water.
            - She went to the counter where the cashier was waiting.
            - The cashier said, "That will be two dollars, please."
        Wynik zapisz w formie prostego kodu JSON:
        [{
        "word": "kasjer",
        "trans": "cashier",
        "sentence":"Poszła do lady, gdzie czekał kasjer.",
        "sentence_trans":"She went to the counter where the cashier was waiting.",
        "emoji":"😀",
        "story_id": 1
        },...]
        Opis pól:
         - word: słowo po polsku
         - trans: jego tłumaczenie na angielski
         - sentence: zdanie po polsku, w którym występuje słowo
         - sentence_trans: tłumacznie zdania na angielski
         – story_id: numer historii, z której pochodzi (story_id).
        Wygeneruj odpowiedź w formacie JSON zawierającą ${{words_count}} rekordów.
        Uwzględnij również specyfikację poziomu języka. Wybrany poziom to: ${{level}}
        ${{letters_condition}}
        Zastosuj:
            - kreatywność w tworzeniu przykładów
            - losowe ziarno generowania: ${{seed}}
        Prompt użytkownika to: ${{category}}.
        Warunek błedu: Jeśli z jakiegoś powodu nie jesteś w stanie wygenerować rekordów dla danej sytuacji, zamiast rekordów odpowiedz w formacie 
        {"error":"prompt"}
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
        $this->prompt = str_replace('${{seed}}', (string) random_int(0, 1000), $this->prompt);
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
