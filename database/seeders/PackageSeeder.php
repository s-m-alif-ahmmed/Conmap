<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PackageOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Packages
        $packages = [
            [
                'title' => 'Free',
                'description' => 'Unleash the power of automation.',
                'price' => 0.00,
                'type' => 'Free',
                'duration' => 14,
                'stripe_product_id' => 'prod_RxS0oK9AUYLLSn',
                'stripe_price_id' => 'price_1R3X6gFPmJRV10EiIYLvSYYp',
                'interval' => 'trail',
                'trial_days' => 14,
                'status' => 'Active',
                'options' => ['14 day trail', '10 pins opens'],
            ],
            [
                'title' => 'Premium',
                'description' => 'Advanced tools to take your work to the next level.',
                'price' => 300.00,
                'type' => 'Month',
                'duration' => 30,
                'stripe_product_id' => 'prod_RxRsqh29IBXTlA',
                'stripe_price_id' => 'price_1R3WyQFPmJRV10EiTSWTcqsh',
                'interval' => 'month',
                'trial_days' => 0,
                'status' => 'Active',
                'options' => ['3 months', '50 pins open a month'],
            ],
            [
                'title' => 'Premium Plus',
                'description' => 'Automation plus enterprise-grade features.',
                'price' => 5160.00,
                'type' => 'Year',
                'duration' => 365,
                'stripe_product_id' => 'prod_RxRtUNPm9JiFdg',
                'stripe_price_id' => 'price_1R3WzdFPmJRV10EiJakRtMNF',
                'interval' => 'year',
                'trial_days' => 0,
                'status' => 'Active',
                'options' => ['1 year', 'Unlimited Pins', 'Updates on site info'],
            ],
        ];

        // Insert into database
        foreach ($packages as $packageData) {
            $package = Package::create([
                'title' => $packageData['title'],
                'description' => $packageData['description'],
                'price' => $packageData['price'],
                'type' => $packageData['type'],
                'duration' => $packageData['duration'],
                'stripe_product_id' => $packageData['stripe_product_id'],
                'stripe_price_id' => $packageData['stripe_price_id'],
                'interval' => $packageData['interval'],
                'trial_days' => $packageData['trial_days'],
                'status' => $packageData['status'],
            ]);

            // Add Package Options
            foreach ($packageData['options'] as $option) {
                PackageOption::create([
                    'package_id' => $package->id,
                    'name' => $option,
                ]);
            }
        }
    }
}
