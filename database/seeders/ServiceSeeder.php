<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Services
        $services = [
            [
                'title' => 'Helps you',
                'description' => 'Generate more leads and expand your business',
                'icon' => null,
                'status' => 'Active',
            ],
            [
                'title' => 'Over 1000 plus',
                'description' => 'Construction sites in London alone',
                'icon' => null,
                'status' => 'Active',
            ],
            [
                'title' => 'Proven to work',
                'description' => 'As i use the same method',
                'icon' => null,
                'status' => 'Active',
            ],
            [
                'title' => 'Small scale projects',
                'description' => 'Such as extension and refurbs to multi million projects',
                'icon' => null,
                'status' => 'Active',
            ],
            [
                'title' => 'Constant updates',
                'description' => 'Sites are updated within 2 months',
                'icon' => null,
                'status' => 'Active',
            ],
            [
                'title' => 'Database consists',
                'description' => 'Of clients, developers, architects and will highlight the tendering process for these companies.',
                'icon' => null,
                'status' => 'Active',
            ],
        ];

        // Insert into database
        foreach ($services as $service) {
            Service::create([
                'title' => $service['title'],
                'description' => $service['description'],
                'icon' => $service['icon'],
                'status' => $service['status'],
            ]);
        }
    }
}
