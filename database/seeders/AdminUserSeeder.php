<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign super_admin role
        $adminUser->assignRole('super_admin');

        // Create job title for admin
        $adminJobTitle = JobTitle::firstOrCreate(
            ['name' => 'System Administrator'],
            ['description' => 'System administration and management']
        );

        // Create employee record for admin
        Employee::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'job_title_id' => $adminJobTitle->id,
                'hire_date' => now(),
                'is_active' => true,
            ]
        );
    }
}
