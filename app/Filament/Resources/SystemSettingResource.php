<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemSettingResource\Pages;
use App\Filament\Resources\SystemSettingResource\RelationManagers;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'Super Admin';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'system_name', 'email', 'number', 'copyright_text', 'address', 'description'];
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('system_name'),
                Forms\Components\TextInput::make('email'),
                Forms\Components\TextInput::make('number')->label('Phone Number'),
                Forms\Components\TextInput::make('tel_number')->label('Telephone Number'),
                Forms\Components\TextInput::make('whatsapp_number')->label('Whatsapp Number'),
                FileUpload::make('logo')
                    ->image()
                    ->maxSize(2048)
                    ->columnSpanFull()
                    ->disk('public')
                    ->directory('uploads/system_settings')
                    ->visibility('public'),
                FileUpload::make('favicon')
                    ->image()
                    ->maxSize(2048)
                    ->columnSpanFull()
                    ->disk('public')
                    ->directory('uploads/system_settings')
                    ->visibility('public'),
                Forms\Components\TextInput::make('copyright_text'),
                Forms\Components\Textarea::make('address'),
                Forms\Components\Textarea::make('description')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->width('auto')->height(50),
                Tables\Columns\ImageColumn::make('favicon')->width(50)->height(50),
                Tables\Columns\TextColumn::make('title')->limit(100)->searchable(),
                Tables\Columns\TextColumn::make('system_name')->limit(100)->searchable(),
                Tables\Columns\TextColumn::make('email')->limit(100)->searchable(),
                Tables\Columns\TextColumn::make('number')->limit(100)->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListSystemSettings::route('/'),
            'view' => Pages\ViewSystemSetting::route('/{record}'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
