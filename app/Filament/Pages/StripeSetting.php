<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms;
use Illuminate\Support\Facades\File;

class StripeSetting extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.stripe-setting';

    public static function canAccess(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['Super Admin']);
    }

    protected static ?string $navigationGroup = 'Settings';

    public $stripe_key;
    public $stripe_secret;
    public $stripe_webhook_secret;


    public function mount()
    {
        $stripe = \App\Models\StripeSetting::first();

        // Populate form with the authenticated user's details
        $this->stripe_key = $stripe->stripe_key ?? env('STRIPE_KEY');
        $this->stripe_secret = $stripe->stripe_secret ?? env('STRIPE_SECRET');
        $this->stripe_webhook_secret = $stripe->stripe_webhook_secret ?? env('STRIPE_WEBHOOK_SECRET');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('stripe_key')
                ->label('Stripe Key')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('stripe_secret')
                ->label('Stripe Secret')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('stripe_webhook_secret')
                ->label('Stripe Webhook Secret')
                ->required()
                ->maxLength(255),
        ];
    }

    protected function handleRecordUpdate($record, array $data)
    {
        $record->update($data);
        return $record;
    }

    public function save()
    {
        $stripe = \App\Models\StripeSetting::first();

        if (!$stripe) {
            // Create new record if not found
            $stripe = \App\Models\StripeSetting::create([
                'stripe_key' => $this->stripe_key,
                'stripe_secret' => $this->stripe_secret,
                'stripe_webhook_secret' => $this->stripe_webhook_secret,
            ]);
        } else {
            // Update existing record
            $stripe->update([
                'stripe_key' => $this->stripe_key,
                'stripe_secret' => $this->stripe_secret,
                'stripe_webhook_secret' => $this->stripe_webhook_secret,
            ]);
        }

        // Update .env file dynamically
        $this->updateEnvFile();

        Notification::make()
            ->body("Stripe settings updated successfully!")
            ->success()
            ->send();
    }

    private function updateEnvFile()
    {
        $envContent = File::get(base_path('.env'));
        $lineBreak  = "\n";
        $envContent = preg_replace([
            '/STRIPE_KEY=(.*)\s*/',
            '/STRIPE_SECRET=(.*)\s*/',
            '/STRIPE_WEBHOOK_SECRET=(.*)\s*/',
        ], [
            'STRIPE_KEY=' . $this->stripe_key . $lineBreak,
            'STRIPE_SECRET=' . $this->stripe_secret . $lineBreak,
            'STRIPE_WEBHOOK_SECRET=' . $this->stripe_webhook_secret . $lineBreak,
        ], $envContent);

        File::put(base_path('.env'), $envContent);
    }

}
