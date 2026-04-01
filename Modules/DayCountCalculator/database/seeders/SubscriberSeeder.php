<?php

namespace Modules\DayCountCalculator\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\DayCountCalculator\Entities\Subscriber;
use Carbon\Carbon;

class SubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = ['calculator', 'comparison', 'footer', 'popup', 'exit_intent'];

        // Create 50 sample subscribers spanning the last 90 days
        for ($i = 1; $i <= 50; $i++) {
            $createdAt = Carbon::now()->subDays(rand(0, 90));
            $isVerified = rand(1, 100) <= 80; // 80% verified
            $isActive = rand(1, 100) <= 95; // 95% active

            $subscriber = Subscriber::create([
                'email' => "subscriber{$i}@example.com",
                'verification_token' => Subscriber::generateVerificationToken(),
                'email_verified_at' => $isVerified ? $createdAt->copy()->addMinutes(rand(5, 120)) : null,
                'is_active' => $isActive,
                'source' => $sources[array_rand($sources)],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('Created 50 sample subscribers');
    }
}
