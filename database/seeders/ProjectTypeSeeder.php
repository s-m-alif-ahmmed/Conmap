<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Project Types
        $project_types = [
            [
                'title' => 'New Build',
                'status' => 'Active',
            ],
            [
                'title' => 'Exiting / Conversion',
                'status' => 'Active',
            ],
            [
                'title' => 'Extensions / Refurbishment / Restoration',
                'status' => 'Active',
            ],
            [
                'title' => 'Land',
                'status' => 'Active',
            ],
            [
                'title' => 'Retail',
                'status' => 'Active',
            ],
            [
                'title' => 'Educational / Schools',
                'status' => 'Active',
            ],
            [
                'title' => 'Place of Worship',
                'status' => 'Active',
            ],
            [
                'title' => 'Community Building',
                'status' => 'Active',
            ],
            [
                'title' => 'Hospital',
                'status' => 'Active',
            ],
            [
                'title' => 'Hotel',
                'status' => 'Active',
            ],
            [
                'title' => 'Prison',
                'status' => 'Active',
            ],
            [
                'title' => 'Museum',
                'status' => 'Active',
            ],
            [
                'title' => 'Data Center',
                'status' => 'Active',
            ],
            [
                'title' => 'Warehouse',
                'status' => 'Active',
            ]
        ];

        // Insert into database
        foreach ($project_types as $type) {
            ProjectType::create([
                'title' => $type['title'],
                'status' => $type['status'],
            ]);
        }
    }
}
