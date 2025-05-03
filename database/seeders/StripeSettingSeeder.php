<?php

namespace Database\Seeders;

use App\Models\StripeSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StripeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StripeSetting::create([
            'stripe_key' => null,
            'stripe_secret' => null,
            'stripe_webhook_secret' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
