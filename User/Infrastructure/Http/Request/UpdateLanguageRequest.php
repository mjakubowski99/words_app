<?php

namespace User\Infrastructure\Http\Request;

use Illuminate\Validation\Rule;
use Shared\Enum\Language as LanguageEnum;
use Shared\Http\Request\Request;
use Shared\Utils\ValueObjects\Language;

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
            },],
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