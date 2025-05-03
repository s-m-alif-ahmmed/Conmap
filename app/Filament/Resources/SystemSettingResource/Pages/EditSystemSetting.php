<?php

namespace App\Filament\Resources\SystemSettingResource\Pages;

use App\Filament\Resources\SystemSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemSetting extends EditRecord
{
    protected static string $resource = SystemSettingResource::class;

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
