<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Credits
        $abouts = [
            [
                'icon' => null,
                'title' => 'Our Vision',
                'description' => 'We aim to revolutionize whe way construction-related businesses connect, collaborate, and grow by offering ni-time, location-based insights into active and upcoming construction projects across the UK.',
                'status' => 'Active',
            ],
            [
                'icon' => null,
                'title' => 'Who We Are',
                'description' => 'ConMap is a smart construction map artform that empowers contractors, suppliers, developers, and service providers by giving them access to verified construction site data. We are a team Of engineers, data analysts, and tech enthusiasts who believe in making constructi6n data more accessible and actionable.',
                'status' => 'Active',
            ],
            [
                'icon' => null,
                'title' => 'What We Do',
                'description' => '• A live map of active ani upcoming construction projects.
                                    • Detailed project infe including equipment, safety zones, and estimated completion dates.
                                    • Contact access to key stakeholders involved in each site.
                                    • Constant database updates for the most accurate and tidy.',
                'status' => 'Active',
            ],
            [
                'icon' => null,
                'title' => 'Our Mission',
                'description' => 'To bridge the gap between construciiction opportuöities and the businesses that serve them.',
                'status' => 'Active',
            ],
        ];

        // Insert into database
        foreach ($abouts as $about) {
            About::create([
                'icon' => $about['icon'],
                'title' => $about['title'],
                'description' => $about['description'],
                'status' => $about['status'],
            ]);

        }
    }
}
