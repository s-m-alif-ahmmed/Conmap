<?php

namespace App\Filament\Resources\DurationResource\Pages;

use App\Filament\Resources\DurationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDuration extends CreateRecord
{
    protected static string $resource = DurationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // Redirect to index page after creation
    }

}
