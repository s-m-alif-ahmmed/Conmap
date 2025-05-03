<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Disable foreign key checks to prevent issues with deletions
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        DB::table('users')->truncate();
        DB::table('system_settings')->truncate();
        DB::table('mail_settings')->truncate();
        DB::table('stripe_settings')->truncate();
        DB::table('packages')->truncate();
        DB::table('credits')->truncate();
        DB::table('services')->truncate();
        DB::table('durations')->truncate();
        DB::table('project_types')->truncate();
        DB::table('units')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Call seeders
        $this->call([
            UserSeeder::class,
            SystemSettingSeeder::class,
            MailSettingSeeder::class,
            StripeSettingSeeder::class,
            PackageSeeder::class,
            CreditSeeder::class,
            ServiceSeeder::class,
            DurationSeeder::class,
            ProjectTypeSeeder::class,
            UnitSeeder::class,
        ]);
    }
}
