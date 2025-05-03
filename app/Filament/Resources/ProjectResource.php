<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Duration;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function canViewAny(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin', 'Admin']);
    }

    public static function canCreate(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin', 'Admin']);
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin', 'Admin']);
    }

    public static function canView($record): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin', 'Admin']);
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    public static function canRestore($record): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    public static function canRestoreAny(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    public static function canForceDelete($record): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'address', 'postal_code', 'client_name', 'local_authority', 'note', 'site_contact', 'latitude', 'longitude', 'end_date', 'site_reference', 'project_build_type', 'land_status', 'visited_status', 'live_status',];
    }

    protected static ?string $navigationGroup = null;

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->role === 'Super Admin' ? 'Projects' : null;
    }


    protected static ?int $navigationSort = 1;

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
                Forms\Components\Select::make('project_type_id')
                    ->label('Project Type')
                    ->relationship('projectType', 'title', fn ($query) => $query->orderBy('id', 'asc'))
                    ->required(),
                Forms\Components\Select::make('project_build_type')
                    ->label('Project Build Type')
                    ->options([
                        'Commercial' => 'Commercial',
                        'Residential' => 'Residential',
                    ])
                    ->required(),
                Forms\Components\Select::make('land_status')
                    ->label('Land or Not')
                    ->options([
                        'Yes' => 'Yes',
                        'No' => 'No',
                    ])
                    ->live()
                    ->required(),
                Forms\Components\Select::make('land_condition')
                    ->label('Land Condition')
                    ->hidden(fn (callable $get) => !$get('land_status') || $get('land_status') === 'No')
                    ->required(fn (callable $get) => $get('land_status') === 'Yes')
                    ->options([
                        'Empty Land' => 'Empty Land',
                        'Potential development opportunity' => 'Potential development opportunity',
                    ]),
                Forms\Components\Select::make('duration_id')
                    ->label('Project Duration')
                    ->hidden(fn (callable $get) => !$get('land_status') || $get('land_status') === 'Yes')
                    ->required(fn (callable $get) => $get('land_status') === 'No')
                    ->relationship('duration', 'duration', fn ($query) => $query->orderBy('id', 'asc')),
                Forms\Components\Select::make('unit_id')
                    ->label('Number of Units')
                    ->hidden(fn (callable $get) => !$get('land_status') || $get('land_status') === 'Yes')
                    ->required(fn (callable $get) => $get('land_status') === 'No')
                    ->relationship('unit', 'title', fn ($query) => $query->orderBy('id', 'asc')),
                Forms\Components\TextInput::make('name')
                    ->label('Project Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('postal_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude')
                    ->label('Map Latitude')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('longitude')
                    ->label('Map Longitude')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('client_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('local_authority')
                    ->label('Local Authority Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('site_contact')
                    ->label('Site Contact'),
                Forms\Components\TextInput::make('site_reference')
                    ->label('Site Reference')
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->label('Notes Section'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Expected Completion Date')
                    ->hidden(fn (callable $get) => !$get('land_status') || $get('land_status') === 'Yes')
                    ->required(fn (callable $get) => $get('land_status') === 'No')
                    ->required(),
                Forms\Components\Select::make('visited_status')
                    ->label('Project Visited Status')
                    ->default('No')
                    ->options([
                        'Yes' => 'Yes',
                        'No' => 'No',
                    ]),
                Forms\Components\Select::make('live_status')
                    ->label('Construction Work Status')
                    ->default('Live')
                    ->options([
                        'Live' => 'Live',
                        'Not Live' => 'Not Live',
                    ]),
                Forms\Components\RichEditor::make('description')
                    ->label('Project Description')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->label('Project Images')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->disk('public')
                    ->directory('uploads/projects')
                    ->visibility('public')
                    ->afterStateHydrated(function (callable $set, ?Project $record) {
                        if ($record && $record->projectImages) {
                            $set('images', $record->projectImages->pluck('image')->toArray());
                        }
                    })
                    ->saveRelationshipsUsing(function (Project $record, $state) {
                        // Delete old images before saving new ones
                        $record->projectImages()->delete();

                        foreach ($state as $image) {
                            if ($image instanceof TemporaryUploadedFile) {
                                $path = $image->store('uploads/projects', 'public');
                                $record->projectImages()->create(['image' => $path]);
                            } else {
                                $record->projectImages()->create(['image' => $image]);
                            }
                        }
                    })
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('projectLinks')
                    ->label('Product Links')
                    ->relationship('projectLinks')
                    ->schema([
                        Forms\Components\TextInput::make('link')
                            ->label('Link'),
                    ])
                    ->createItemButtonLabel('Add New Link'),
                Forms\Components\Repeater::make('projectContacts')
                    ->label('Project Contacts')
                    ->relationship('projectContacts')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name'),
                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->createItemButtonLabel('Add New Link'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('Created By')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->user?->first_name . ' ' . $record->user?->last_name ?? 'N/A'),
                Tables\Columns\TextColumn::make('project_type_id')
                    ->label('Project Type')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->projectType?->title ?? 'N/A'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_build_type'),
                Tables\Columns\TextColumn::make('land_status'),
                Tables\Columns\TextColumn::make('visited_status'),
                Tables\Columns\TextColumn::make('live_status'),
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
                            ->body("Project {$record->name} status changed to {$state}.")
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
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => in_array(auth()->user()->role, ['Super Admin'])),
                    Tables\Actions\ExportAction::make()
                        ->visible(fn () => in_array(auth()->user()->role, ['Super Admin'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

}
