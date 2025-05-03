<?php

namespace App\Filament\Resources\CreditResource\Pages;

use App\Filament\Resources\CreditResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCredit extends EditRecord
{
    protected static string $resource = CreditResource::class;

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
