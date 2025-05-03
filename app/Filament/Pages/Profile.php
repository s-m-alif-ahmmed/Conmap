<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.profile';

    public $first_name;
    public $last_name;
    public $email;

    public function mount()
    {
        $user = Auth::user();

        // Populate form with the authenticated user's details
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('first_name')
                ->label('First Name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('last_name')
                ->label('Last Name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('Email Address')
                ->email()
                ->columnSpanFull()
                ->disabled()
                ->required(),
        ];
    }

    protected function handleRecordUpdate(Authenticatable $record, array $data): Authenticatable
    {
        $record->update($data);
        return $record;
    }

    public function save()
    {
        $user = Auth::user();
        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ]);

        Notification::make()
            ->body("Profile updated successfully!")
            ->success()
            ->send();
    }

}
