<?php

declare(strict_types=1);

namespace Admin\Traits;

use Filament\Tables;
use Admin\Models\Flashcard;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Shared\Flashcard\IFlashcardAdminFacade;
use Shared\Models\Emoji;

trait HasFlashcardConfigurator
{
    public static function tableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('emoji')->formatStateUsing(function (Model $model) {
                return $model->emoji ? (string) Emoji::fromUnicode($model->emoji) : null;
            }),
            Tables\Columns\TextColumn::make('front_context')->sortable(),
            Tables\Columns\TextColumn::make('front_word')->sortable(),
            Tables\Columns\TextColumn::make('back_context')->sortable(),
            Tables\Columns\TextColumn::make('back_word')->sortable(),
        ];
    }

    public static function formFields(): array
    {
        return [
            TextInput::make('front_word'),
            TextInput::make('front_context'),
            TextInput::make('back_word'),
            TextInput::make('back_context'),
        ];
    }

    public static function buildFlashcardFacade(): IFlashcardAdminFacade
    {
        /* @var IFlashcardAdminFacade */
        return app()->make(IFlashcardAdminFacade::class);
    }

    public static function buildEditAction(): Tables\Actions\EditAction
    {
        return Tables\Actions\EditAction::make()
            ->using(function (Flashcard $flashcard, array $data) {
                self::buildFlashcardFacade()->update(
                    $flashcard->id,
                    $data['front_word'],
                    $data['front_context'],
                    $data['back_word'],
                    $data['back_context'],
                );

                return $flashcard;
            });
    }

    public static function buildDeleteAction(): Tables\Actions\DeleteAction
    {
        return Tables\Actions\DeleteAction::make()
            ->action(function (Flashcard $flashcard) {
                self::buildFlashcardFacade()->delete($flashcard->id);
            });
    }
}
