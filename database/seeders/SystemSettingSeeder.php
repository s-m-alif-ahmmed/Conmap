<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
            'title' => 'CONMAP',
            'system_name' => 'CONMAP',
            'email' => 'info@conmap.com',
            'number' => '5873515720',
            'tel_number' => '02034887875',
            'whatsapp_number' => '5873515720',
            'logo' => '/frontend/logo.png',
            'favicon' => '/frontend/favicon.png',
            'address' => null,
            'copyright_text' => 'Copyright 2025. All Rights Reserved. Powered by CONMAP.',
            'description' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
