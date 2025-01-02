<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashcardResource\Pages;
use App\Filament\Resources\FlashcardResource\RelationManagers;
use App\Models\Flashcard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlashcardResource extends Resource
{
    protected static ?string $model = Flashcard::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('front_word')->sortable(),
                Tables\Columns\TextColumn::make('back_word')->sortable(),
                Tables\Columns\TextColumn::make('front_context')->sortable(),
                Tables\Columns\TextColumn::make('back_context')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlashcards::route('/'),
            'create' => Pages\CreateFlashcard::route('/create'),
            'edit' => Pages\EditFlashcard::route('/{record}/edit'),
        ];
    }
}
