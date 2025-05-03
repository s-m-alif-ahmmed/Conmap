<?php

namespace App\Filament\Resources\DurationResource\Pages;

use App\Filament\Resources\DurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDuration extends EditRecord
{
    protected static string $resource = DurationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // Redirect to index page after creation
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
