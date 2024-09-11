<?php

namespace Flashcard\Infrastructure\Http\Request;

use App\Models\User;
use Shared\Http\Request\Request;
use Shared\User\IUser;
use Shared\Utils\ValueObjects\UserId;

class GenerateFlashcardsRequest extends Request
{
    public function rules(): array
    {
        return [
            'category_name' => ['required', 'string'],
        ];
    }

    public function resolveUser(): IUser
    {
        $mock = \Mockery::mock(IUser::class);

        $mock->shouldReceive('getId')->andReturn(new UserId(User::query()->first()->id));

        return $mock;
    }

    public function getCategoryName()
    {
        return $this->input('category_name');
    }
}