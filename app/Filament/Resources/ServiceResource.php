<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

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
        return ['title', 'description'];
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('icon')
                    ->image()
                    ->acceptedFileTypes(['image/svg+xml'])
                    ->maxSize(2048)
                    ->columnSpanFull()
                    ->disk('public')
                    ->directory('uploads/services')
                    ->visibility('public'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')->width('auto')->height(50),
                Tables\Columns\TextColumn::make('title')->searchable(),
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
                            ->body("Service {$record->title} status changed to {$state}.")
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
                Tables\Actions\DeleteAction::make()->after(function ($record) {
                    if ($record->icon && file_exists(public_path($record->icon))) {
                        unlink(public_path($record->icon));
                    }
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->after(function ($records) {
                        foreach ($records as $record) {
                            if ($record->icon && file_exists(public_path($record->icon))) {
                                unlink(public_path($record->icon));
                            }
                        }
                    })->visible(fn () => auth()->check() && in_array(auth()->user()->role, ['Super Admin']) ),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
