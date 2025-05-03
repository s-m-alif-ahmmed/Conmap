<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditResource\Pages;
use App\Filament\Resources\CreditResource\RelationManagers;
use App\Models\Credit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditResource extends Resource
{
    protected static ?string $model = Credit::class;

    public static function canViewAny(): bool
    {
//        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
        return false;
    }

    public static function canCreate(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    public static function canView($record): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }


    public static function canDelete($record): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['price', 'title', 'bonus'];
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('€'),
                Forms\Components\TextInput::make('bonus')
                    ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bonus')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?? 'N/A'),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ])
                    ->default(fn ($record) => $record?->status ?? 'Active')
                    ->inline()
                    ->sortable()
                    ->searchable()
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->body("Credit {$record->title} status changed to {$state}.")
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => auth()->check() && in_array(auth()->user()->role, ['Super Admin']) ),
                    Tables\Actions\ExportAction::make()->visible(fn () => auth()->check() && in_array(auth()->user()->role, ['Super Admin']) ),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCredits::route('/'),
            'create' => Pages\CreateCredit::route('/create'),
            'view' => Pages\ViewCredit::route('/{record}'),
            'edit' => Pages\EditCredit::route('/{record}/edit'),
        ];
    }
}
