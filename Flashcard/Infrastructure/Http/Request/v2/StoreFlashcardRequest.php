<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use OpenApi\Attributes as OAT;
use Shared\Enum\LanguageLevel;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Application\Command\CreateFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

#[OAT\Schema(
    schema: 'Requests\Flashcard\v2\StoreFlashcardRequest',
    properties: [
        new OAT\Property(
            property: 'flashcard_deck_id',
            description: 'Deck id to update',
            type: 'integer',
            example: 1,
        ),
        new OAT\Property(
            property: 'front_word',
            description: 'Flashcard front word',
            type: 'string',
            example: 'Jabłko',
        ),
        new OAT\Property(
            property: 'back_word',
            description: 'Flashcard back word',
            type: 'string',
            example: 'Apple',
        ),
        new OAT\Property(
            property: 'front_context',
            description: 'Front flashcard context sentence',
            type: 'string',
            example: 'Adam je jabłko',
        ),
        new OAT\Property(
            property: 'back_context',
            description: 'Back flashcard context sentence',
            type: 'string',
            example: 'Adam eats apple',
        ),
        new OAT\Property(
            property: 'language_level',
            description: 'Language level. Default value is: ' . LanguageLevel::DEFAULT,
            type: 'string',
            enum: [
                LanguageLevel::A1,
                LanguageLevel::A2,
                LanguageLevel::B1,
                LanguageLevel::B1,
                LanguageLevel::C1,
                LanguageLevel::C2,
            ],
            example: LanguageLevel::C1,
            nullable: true
        ),
    ]
)]
class StoreFlashcardRequest extends Request
{
    public function rules(): array
    {
        return [
            'flashcard_deck_id' => ['required', 'integer'],
            'front_word' => ['required', 'string', 'max:255'],
            'back_word' => ['required', 'string', 'max:255'],
            'front_context' => ['required', 'string', 'max:255'],
            'back_context' => ['required', 'string', 'max:255'],
            'language_level' => ['nullable', 'string'],
        ];
    }

    public function toCommand(): CreateFlashcard
    {
        $user = $this->current();

        return new CreateFlashcard(
            Owner::fromUser($user->getId()),
            new FlashcardDeckId($this->input('flashcard_deck_id')),
            Language::pl(),
            $this->input('front_word'),
            $this->input('front_context'),
            Language::en(),
            $this->input('back_word'),
            $this->input('back_context'),
            $this->input('language_level') ? LanguageLevel::from($this->input('language_level')) : LanguageLevel::default()
        );
    }
}
