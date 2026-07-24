<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Credentials come from the environment (SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD).
     * If no password is provided, a random one is generated and shown once so it is
     * never committed to source control.
     */
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL');

        if (! $email) {
            $this->command->warn('SUPER_ADMIN_EMAIL is not set. Skipping super admin seeding.');
            $this->command->warn('Set SUPER_ADMIN_EMAIL (and optionally SUPER_ADMIN_PASSWORD) in .env, then re-run.');

            return;
        }

        $existingAdmin = User::where('email', $email)->first();

        if ($existingAdmin) {
            $this->command->info('Super admin already exists. Updating role...');
            $existingAdmin->role = User::ROLE_SUPER_ADMIN;
            $existingAdmin->save();
        } else {
            $password = env('SUPER_ADMIN_PASSWORD') ?: Str::password(20);

            $user = new User([
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $user->role = User::ROLE_SUPER_ADMIN;
            $user->email_verified_at = now();
            $user->save();

            if (! env('SUPER_ADMIN_PASSWORD')) {
                $this->command->info('Generated one-time password: '.$password);
                $this->command->warn('Store it securely and change it after first login.');
            }
        }

        $this->command->info('Super admin setup complete for: '.$email);
    }
}
