<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates default admin account with manager role (highest level).
     * Role system: Manager (highest), Staff (standard)
     */
    public function run(): void
    {

        $existingAdmin = User::where('email', 'admin@dinoyokamera.com')->first();
        
        if (!$existingAdmin) {

            $manager = Employee::where('jabatan', 'Manager')->first();
            
            if ($manager) {

                User::create([
                    'id' => $manager->id,
                    'name' => $manager->nama_lengkap,
                    'email' => 'admin@dinoyokamera.com',
                    'password' => Hash::make('admin123'), // Password default
                    'role' => 'manager', // Role manager (highest level)
                    'status' => 'active', // Langsung aktif, tidak pending
                    'email_verified_at' => now(), // Langsung verified

                ]);

                $this->command->info('✅ Admin account created successfully!');
                $this->command->info('   Email: admin@dinoyokamera.com');
                $this->command->info('   Password: admin123');
                $this->command->info('   Role: Manager');
                $this->command->info('   Status: Active (Auto-login ready)');
            } else {
                $this->command->error('❌ No manager employee found! Please run KaryawanSeeder first.');
            }
        } else {
            $this->command->info('ℹ️  Admin account already exists!');
        }
    }
}
