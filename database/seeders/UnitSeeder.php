<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Units
        $units = [
            [
                'title' => 'Single unit',
                'status' => 'Active',
            ],
            [
                'title' => '1-5 units',
                'status' => 'Active',
            ],
            [
                'title' => '5-15 units',
                'status' => 'Active',
            ],
            [
                'title' => '15-40 units',
                'status' => 'Active',
            ],
            [
                'title' => '40-100 units',
                'status' => 'Active',
            ],
            [
                'title' => '100-200 units',
                'status' => 'Active',
            ],
            [
                'title' => '200-500 units',
                'status' => 'Active',
            ],
            [
                'title' => '500-1000 units',
                'status' => 'Active',
            ],
            [
                'title' => '1000 greater/ regeneration Projects',
                'status' => 'Active',
            ]
        ];

        // Insert into database
        foreach ($units as $unit) {
            Unit::create([
                'title' => $unit['title'],
                'status' => $unit['status'],
            ]);
        }
    }
}
