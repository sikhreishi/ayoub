<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;

use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure roles exist
        $roles = ['admin', 'driver', 'user'];
        $users = [];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create Admin if not exists
        if (!User::where('email', 'admin@admin.com')->exists()) {
            $users[]= User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                "phone"=> '1234567',

                'password' => bcrypt('password'),
            ])->assignRole('admin');

        }

        // Create Driver if not exists
        if (!User::where('email', 'driver@example.com')->exists()) {
            $users[]= User::create([
                'name' => 'Driver User',
                'email' => 'driver@example.com',
                'password' => bcrypt('password'),
                "phone"=> '12345678',
            ])->assignRole('driver');
        }

        // Create Regular User if not exists
        if (!User::where('email', 'user@example.com')->exists()) {
            $users[]= User::create([
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'),
                "phone"=> '123456789',

            ])->assignRole('user');
        }
      
    }
}
