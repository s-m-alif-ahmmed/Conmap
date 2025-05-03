<?php

namespace Database\Seeders;

use App\Models\Duration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Durations
        $durations = [
            [
                'duration' => '1-3 months',
                'status' => 'Active',
            ],
            [
                'duration' => '1-6 months',
                'status' => 'Active',
            ],
            [
                'duration' => '1-9 months',
                'status' => 'Active',
            ],
            [
                'duration' => '1-12 months',
                'status' => 'Active',
            ],
            [
                'duration' => '1 year to 2 years',
                'status' => 'Active',
            ],
            [
                'duration' => '1 year to 5 years',
                'status' => 'Active',
            ],
        ];

        // Insert into database
        foreach ($durations as $duration) {
            Duration::create([
                'duration' => $duration['duration'],
                'status' => $duration['status'],
            ]);
        }
    }
}
