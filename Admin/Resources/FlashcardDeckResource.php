<?php

declare(strict_types=1);

namespace Admin\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Admin\Models\FlashcardDeck;
use Filament\Resources\Resource;
use Admin\Traits\HasFlashcardDeckConfigurator;
use Admin\Resources\FlashcardDeckResource\Pages\EditFlashcardDeck;
use Admin\Resources\FlashcardDeckResource\Pages\ListFlashcardDecks;
use Admin\Resources\FlashcardDeckResource\Pages\CreateFlashcardDeck;
use Admin\Resources\FlashcardDeckResource\RelationManagers\FlashcardsRelationManager;

class FlashcardDeckResource extends Resource
{
    use HasFlashcardDeckConfigurator;

    protected static ?string $model = FlashcardDeck::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::formFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::tableColumns())
            ->actions([
                Tables\Actions\EditAction::make(),
            ])->headerActions([
                self::buildImportAction(),
                self::buildExportAction(),
                self::buildUpsertDeckNames(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FlashcardsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFlashcardDecks::route('/'),
            'create' => CreateFlashcardDeck::route('/create'),
            'edit' => EditFlashcardDeck::route('/{record}/edit'),
        ];
    }
}
