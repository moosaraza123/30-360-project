<?php

namespace Modules\DayCountCalculator\Database\Seeders;

use Illuminate\Database\Seeder;

class DayCountCalculatorDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CalculationSeeder::class,
            SubscriberSeeder::class,
        ]);
    }
}
