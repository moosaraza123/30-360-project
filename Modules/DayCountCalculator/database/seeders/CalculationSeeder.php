<?php

namespace Modules\DayCountCalculator\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\DayCountCalculator\Entities\Calculation;
use Carbon\Carbon;

class CalculationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conventions = [
            '30/360 US',
            '30/360 Bond Basis',
            '30E/360',
            '30E/360 ISDA',
            'Actual/365 Fixed',
            'Actual/360',
            'Actual/364',
            'Actual/Actual',
            'Actual/Actual ISDA',
        ];

        $sessionIds = [];
        for ($i = 0; $i < 10; $i++) {
            $sessionIds[] = bin2hex(random_bytes(20));
        }

        // Create 100 sample calculations spanning the last 90 days
        for ($i = 0; $i < 100; $i++) {
            $convention = $conventions[array_rand($conventions)];
            $createdAt = Carbon::now()->subDays(rand(0, 90));

            // Random date range (1 to 365 days apart)
            $startDate = $createdAt->copy()->subDays(rand(0, 180));
            $daysBetween = rand(30, 365);
            $endDate = $startDate->copy()->addDays($daysBetween);

            // Calculate actual days
            $actualDays = $startDate->diffInDays($endDate);

            // Simplified day count factor (not accurate, just for seeding)
            $dayCountFactor = match(true) {
                str_contains($convention, '30/360') => $actualDays / 360,
                str_contains($convention, 'Actual/365') => $actualDays / 365,
                str_contains($convention, 'Actual/360') => $actualDays / 360,
                str_contains($convention, 'Actual/364') => $actualDays / 364,
                default => $actualDays / 365.25,
            };

            // 60% of calculations have interest calculations
            $hasInterest = rand(1, 100) <= 60;
            $principal = $hasInterest ? rand(100000, 10000000) / 100 : null;
            $interestRate = $hasInterest ? rand(100, 1000) / 10000 : null; // 1% to 10%
            $interestAmount = $hasInterest ? $principal * $interestRate * $dayCountFactor : null;

            // Simple calculation steps
            $steps = [
                [
                    'title' => 'Calculate Days',
                    'description' => "Calculated {$actualDays} days between dates",
                    'formula' => "Days = {$actualDays}",
                    'applied' => true,
                ],
                [
                    'title' => 'Calculate Day Count Factor',
                    'description' => 'Applied day count convention formula',
                    'formula' => sprintf('Factor = %.10f', $dayCountFactor),
                    'applied' => true,
                ],
            ];

            if ($hasInterest) {
                $steps[] = [
                    'title' => 'Calculate Interest',
                    'description' => 'Calculated interest amount',
                    'formula' => sprintf('Interest = $%.2f', $interestAmount),
                    'applied' => true,
                ];
            }

            Calculation::create([
                'convention_type' => $convention,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days_calculated' => $actualDays,
                'day_count_factor' => $dayCountFactor,
                'principal' => $principal,
                'interest_rate' => $interestRate,
                'interest_amount' => $interestAmount,
                'calculation_steps' => $steps,
                'session_id' => $sessionIds[array_rand($sessionIds)],
                'user_id' => null, // Set to null for seeded data (no actual users)
                'ip_address' => $this->randomIp(),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('Created 100 sample calculations');
    }

    /**
     * Generate a random IP address
     */
    private function randomIp(): string
    {
        return implode('.', [
            rand(1, 255),
            rand(0, 255),
            rand(0, 255),
            rand(0, 255)
        ]);
    }
}
