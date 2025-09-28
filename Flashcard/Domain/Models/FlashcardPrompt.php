<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Exceptions\InvalidPromptException;

class FlashcardPrompt
{
    private string $prompt = '
        You are an AI algorithm generating vocabulary for language learning.
        Based on the topic provided by the user, create a story consisting of ${{words_count}} sentences in ${{translation_lang_name}}.
        Divide the story into parts that must have 3-4 sentences each — each part is a separate mini-story,
        which within these sentences must form a coherent, logical whole (i.e., a short event with beginning, middle, and end).
        For each sentence, generate its translation into ${{word_lang_name}}.
        Then extract words for flashcards from the generated sentences. Selected words must:
        – appear in the sentence in their basic (uninflected) form,
        – directly relate to the topic,
        – not repeat in other stories.
        Example story: conversation with a cashier
            - Emma walked into the store and picked up a bottle of water.
            - She went to the counter where the cashier was waiting.
            - The cashier said, "That will be two dollars, please."
        Save the result in simple JSON code format:
        [{
        "word": "word_in_${{word_lang_code}}",
        "trans": "translation_in_${{translation_lang_code}}",
        "sentence": "sentence_in_${{word_lang_name}}",
        "sentence_trans": "sentence_in_${{translation_lang_name}}",
        "emoji": "😀",
        "story_id": 1
        },...]
        Field descriptions:
         - word: word in ${{word_lang_name}}
         - trans: its translation to ${{translation_lang_name}}
         - sentence: sentence in ${{word_lang_name}} containing the word
         - sentence_trans: sentence translation to ${{translation_lang_name}}
         - story_id: story number from which it originates (story_id).
        Generate a JSON format response containing ${{words_count}} records.
        Also consider the language level specification. Selected level: ${{level}}
        ${{letters_condition}}
        Apply:
            - creativity in creating examples
            - random generation seed: ${{seed}}
        User prompt: ${{category}}.
        Error condition: If for any reason you cannot generate records for the given situation, instead of records respond in format 
        {"error":"prompt"}
        Your response should contain only and exclusively data in JSON format and nothing else.
    ';

    public function __construct(
        private readonly string $category,
        private readonly LanguageLevel $language_level,
        private readonly Language $word_lang,
        private readonly Language $translation_lang,
        private readonly int $words_count = 10,
        private array $initial_letters_to_avoid = [],
    ) {
        $this->validateLanguages();
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

    private function validateLanguages(): void
    {
        $supportedLanguages = ['pl', 'en', 'it', 'es', 'fr', 'de', 'zh', 'cs'];

        if (!in_array($this->word_lang->getValue(), $supportedLanguages, true)) {
            throw new InvalidPromptException("Unsupported word language: {$this->word_lang->getValue()}");
        }

        if (!in_array($this->translation_lang->getValue(), $supportedLanguages, true)) {
            throw new InvalidPromptException("Unsupported translation language: {$this->translation_lang->getValue()}");
        }
    }

    private function buildPrompt(): void
    {
        $this->setRandomSeed();
        $this->setCategory();
        $this->setLanguageLevel();
        $this->setWordsCount();
        $this->setLanguages();
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

    private function setLanguages(): void
    {
        $replacements = [
            '${{word_lang_name}}' => $this->word_lang->getValue(),
            '${{translation_lang_name}}' => $this->translation_lang->getValue(),
            '${{word_lang_code}}' => $this->word_lang->getValue(),
            '${{translation_lang_code}}' => $this->translation_lang->getValue(),
        ];

        foreach ($replacements as $placeholder => $replacement) {
            if (!str_contains($this->prompt, $placeholder)) {
                throw new InvalidPromptException("Invalid prompt exception. Missing placeholder: {$placeholder}");
            }
            $this->prompt = str_replace($placeholder, $replacement, $this->prompt);
        }
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
            $condition = 'Avoid words starting with letters: ';
            $this->prompt = str_replace('${{letters_condition}}', $condition . $letters, $this->prompt);
        }
    }

    private function removeWhiteCharacters(): void
    {
        $this->prompt = str_replace(["\n", "\r"], '', $this->prompt);
    }
}
