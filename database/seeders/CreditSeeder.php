<?php

namespace Database\Seeders;

use App\Models\Credit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Credits
        $credits = [
            [
                'price' => 10.00,
                'title' => 'Standard package with no bonus',
                'bonus' => null,
                'status' => 'Active',
            ],
            [
                'price' => 100.00,
                'title' => '+ € 5 bonus',
                'bonus' => '5',
                'status' => 'Active',
            ],
            [
                'price' => 1000.00,
                'title' => '+ € 15 bonus',
                'bonus' => '15',
                'status' => 'Active',
            ],
        ];

        // Insert into database
        foreach ($credits as $credit) {
            Credit::create([
                'price' => $credit['price'],
                'title' => $credit['title'],
                'bonus' => $credit['bonus'],
                'status' => $credit['status'],
            ]);

        }
    }
}
