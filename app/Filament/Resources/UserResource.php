<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function canViewAny(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
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
        return ['first_name', 'last_name', 'email', 'role'];
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        $model = static::$model;
        return $model::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->string()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->string()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->rules(fn ($record) => [
                        Rule::unique('users', 'email')->ignore($record?->id),
                    ]),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->autocomplete('new-password')
                    ->minLength(8)
                    ->nullable() // Allow null, so it's not required when editing
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state)) // Ensures the field is only saved if changed
                    ->required(fn ($record) => $record === null),
                Forms\Components\Select::make('role')
                    ->options([
                        'Super Admin' => 'Super Admin',
                        'Admin' => 'Admin',
                        'User' => 'User',
                    ])
                    ->default('User'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
//            ->query(fn (User $query) => $query->where('role', '!=', 'Super Admin'))
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->limit(100)->searchable(),
                Tables\Columns\TextColumn::make('email')->limit(100)->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable()
                    ->limit(100),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
