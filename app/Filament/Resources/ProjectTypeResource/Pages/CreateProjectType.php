<?php

namespace App\Filament\Resources\ProjectTypeResource\Pages;

use App\Filament\Resources\ProjectTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectType extends CreateRecord
{
    protected static string $resource = ProjectTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // Redirect to index page after creation
    }

}
