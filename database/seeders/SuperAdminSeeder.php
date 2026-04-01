<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        $existingAdmin = User::where('email', 'razamoosa538@gmail.com')->first();

        if ($existingAdmin) {
            $this->command->info('Super admin already exists. Updating role...');
            $existingAdmin->update(['role' => User::ROLE_SUPER_ADMIN]);
        } else {
            $this->command->info('Creating super admin user...');
            User::create([
                'name' => 'Raza Moosa',
                'email' => 'razamoosa538@gmail.com',
                'password' => Hash::make('G5@ZM39z'),
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Super admin setup complete!');
        $this->command->info('Email: razamoosa538@gmail.com');
        $this->command->info('Role: super_admin');
    }
}
