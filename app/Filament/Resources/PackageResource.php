<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use App\Models\PackageOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

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
        return ['title', 'type', 'price', 'description'];
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        $model = static::$model;
        return $model::where('status', 'Active')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('£'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('duration')
                    ->numeric(),
                Forms\Components\TextInput::make('stripe_product_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('stripe_price_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('interval')
                    ->options([
                        'trail' => 'Trail',
                        'month' => 'Monthly',
                        'year' => 'Yearly',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('trial_days')
                    ->numeric()
                    ->required(),
                Forms\Components\Repeater::make('packageOptions') // Use relation method, not 'package_options'
                    ->label('Package Options')
                    ->relationship('packageOptions') // Attach to the relationship
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Option Name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columnSpanFull()
                    ->createItemButtonLabel('Add More Option')
                    ->disableItemDeletion(false)
                    ->disableItemCreation(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->numeric()
                    ->sortable(),
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
                            ->body("Package {$record->title} status changed to {$state}.")
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'view' => Pages\ViewPackage::route('/{record}'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }

}
