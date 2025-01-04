<?php

declare(strict_types=1);

namespace Admin\Resources;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Admin\Models\Flashcard;
use Filament\Resources\Resource;
use Admin\Traits\HasFlashcardConfigurator;
use Admin\Resources\FlashcardResource\Pages\EditFlashcard;
use Admin\Resources\FlashcardResource\Pages\ListFlashcards;
use Admin\Resources\FlashcardResource\Pages\CreateFlashcard;

class FlashcardResource extends Resource
{
    use HasFlashcardConfigurator;

    protected static ?string $model = Flashcard::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema(self::formFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::tableColumns())
            ->actions([
                self::buildEditAction(),
                self::buildDeleteAction(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFlashcards::route('/'),
            'create' => CreateFlashcard::route('/create'),
            'edit' => EditFlashcard::route('/{record}/edit'),
        ];
    }
}
