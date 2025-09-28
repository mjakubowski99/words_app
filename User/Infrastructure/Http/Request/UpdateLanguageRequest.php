<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Illuminate\Validation\Rule;
use Shared\Http\Request\Request;
use Shared\Utils\ValueObjects\Language;
use Shared\Enum\Language as LanguageEnum;

#[OAT\Schema(
    schema: 'Requests\User\UpdateLanguageRequest',
    required: ['user_language', 'learning_language'],
    properties: [
        new OAT\Property(
            property: 'user_language',
            description: 'User\'s native language',
            type: 'string',
            enum: [
                LanguageEnum::DE,
                LanguageEnum::EN,
                LanguageEnum::ES,
                LanguageEnum::PL,
                LanguageEnum::ZH,
                LanguageEnum::CS,
                LanguageEnum::FR,
                LanguageEnum::IT,
            ]
        ),
        new OAT\Property(
            property: 'learning_language',
            description: 'Language the user wants to learn (must be different from user_language)',
            type: 'string',
            enum: [
                LanguageEnum::DE,
                LanguageEnum::EN,
                LanguageEnum::ES,
                LanguageEnum::PL,
                LanguageEnum::ZH,
                LanguageEnum::CS,
                LanguageEnum::FR,
                LanguageEnum::IT,
            ]
        ),
    ]
)]
class UpdateLanguageRequest extends Request
{
    public function rules(): array
    {
        return [
            'user_language' => ['required', Rule::enum(LanguageEnum::class)],
            'learning_language' => ['required', Rule::enum(LanguageEnum::class), function ($attribute, $value, $fail) {
                if ($value === $this->input('user_language')) {
                    $fail('The learning language must be different from your native language.');
                }
            }, ],
        ];
    }

    public function getUserLanguage(): Language
    {
        return Language::from($this->input('user_language'));
    }

    public function getLearningLanguage(): Language
    {
        return Language::from($this->input('learning_language'));
    }
}
