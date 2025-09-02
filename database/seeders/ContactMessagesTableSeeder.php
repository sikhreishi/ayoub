<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactMessage;
use App\Models\User;
use Faker\Factory as Faker;

class ContactMessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_US');

        // Get users with 'user' role
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->take(3)->get();

        // Get users with 'driver' role
        $drivers = User::whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->take(3)->get();

        // Create messages for users with 'user' role
        foreach ($users as $user) {
            ContactMessage::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $faker->phoneNumber(),
                'message' => $faker->paragraph(3),
                'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
                'updated_at' => $faker->dateTimeBetween('-30 days', 'now'),
            ]);
        }

        // Create messages for users with 'driver' role
        foreach ($drivers as $driver) {
            ContactMessage::create([
                'user_id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'phone' => $faker->phoneNumber(),
                'message' => $faker->paragraph(3),
                'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
                'updated_at' => $faker->dateTimeBetween('-30 days', 'now'),
            ]);
        }

        // Create messages from guests (without user_id)
        for ($i = 0; $i < 5; $i++) {
            ContactMessage::create([
                'user_id' => null,
                'name' => $faker->name(),
                'email' => $faker->email(),
                'phone' => $faker->phoneNumber(),
                'message' => $faker->paragraph(2),
                'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
                'updated_at' => $faker->dateTimeBetween('-30 days', 'now'),
            ]);
        }

        // Create some recent messages from users
        for ($i = 0; $i < 3; $i++) {
            $randomUser = $users->random();
            ContactMessage::create([
                'user_id' => $randomUser->id,
                'name' => $randomUser->name,
                'email' => $randomUser->email,
                'phone' => $faker->phoneNumber(),
                'message' => $faker->paragraph(4),
                'created_at' => $faker->dateTimeBetween('-7 days', 'now'),
                'updated_at' => $faker->dateTimeBetween('-7 days', 'now'),
            ]);
        }

        // Create some recent messages from drivers
        for ($i = 0; $i < 3; $i++) {
            $randomDriver = $drivers->random();
            ContactMessage::create([
                'user_id' => $randomDriver->id,
                'name' => $randomDriver->name,
                'email' => $randomDriver->email,
                'phone' => $faker->phoneNumber(),
                'message' => $faker->paragraph(4),
                'created_at' => $faker->dateTimeBetween('-7 days', 'now'),
                'updated_at' => $faker->dateTimeBetween('-7 days', 'now'),
            ]);
        }
    }
}
