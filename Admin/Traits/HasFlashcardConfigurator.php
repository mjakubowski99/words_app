<?php

declare(strict_types=1);

namespace Admin\Traits;

use Shared\Models\Emoji;
use Admin\Models\Flashcard;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Shared\Flashcard\IFlashcardAdminFacade;

trait HasFlashcardConfigurator
{
    public static function tableColumns(): array
    {
        return [
            TextColumn::make('id')->sortable(),
            TextColumn::make('emoji')->formatStateUsing(function (Flashcard $model) {
                return $model->emoji ? (string) Emoji::fromUnicode($model->emoji) : null;
            }),
            TextColumn::make('front_context')->sortable(),
            TextColumn::make('front_word')->sortable(),
            TextColumn::make('back_context')->sortable(),
            TextColumn::make('back_word')->sortable(),
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

    public static function buildEditAction(): EditAction
    {
        return EditAction::make()
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

    public static function buildDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->action(function (Flashcard $flashcard) {
                self::buildFlashcardFacade()->delete($flashcard->id);
            });
    }
}
