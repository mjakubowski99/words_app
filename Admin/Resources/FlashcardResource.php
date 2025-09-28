<?php

declare(strict_types=1);

namespace Admin\Resources;

use Filament\Tables\Table;
use Admin\Models\Flashcard;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Admin\Traits\HasFlashcardConfigurator;
use Admin\Resources\FlashcardResource\Pages\EditFlashcard;
use Admin\Resources\FlashcardResource\Pages\ListFlashcards;
use Admin\Resources\FlashcardResource\Pages\CreateFlashcard;

class FlashcardResource extends Resource
{
    use HasFlashcardConfigurator;

    protected static ?string $model = Flashcard::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(self::formFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::tableColumns())
            ->recordActions([
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
