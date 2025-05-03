<?php

namespace App\Filament\Resources\DurationResource\Pages;

use App\Filament\Resources\DurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDuration extends ViewRecord
{
    protected static string $resource = DurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
