<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CountriesTableSeeder;
use Database\Seeders\CitiesTableSeeder;
use Database\Seeders\AddressesTableSeeder;
use Database\Seeders\DistrictsTableSeeder;
use Database\Seeders\ContactMessagesTableSeeder;
use Database\Seeders\WalletCodePermissionSeeder;
use App\Models\User;
use App\Models\Trip;
use App\Models\TripReview;
use App\Models\PaymentGateway;
use App\Models\Invoice;
use App\Models\Currency;


class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            CountriesTableSeeder::class,
            CitiesTableSeeder::class,
            AddressesTableSeeder::class,
            DistrictsTableSeeder::class,
            VehicleTypesSeeder::class,
            ContactMessagesTableSeeder::class,
            TicketCategoriesSeeder::class,
            TicketSeeder::class,
            WalletCodePermissionSeeder::class,
        ]);


        $user = User::create([
            'name' => 'Test Rider',
            'email' => 'rider@example.com',
            'password' => bcrypt('password'),
            "phone" => '1234567891',
        ])->assignRole('user');

        $driver = User::create([
            'name' => 'Test Driver2',
            'email' => 'driver2@example.com',
            'password' => bcrypt('password'),
            "phone" => '12345678912',
        ])->assignRole('driver');

        $trip = Trip::create([
            'user_id' => $user->id,
            'driver_id' => $driver->id,
            'pickup_lat' => 24.7136,
            'pickup_lng' => 46.6753,
            'dropoff_lat' => 24.7743,
            'dropoff_lng' => 46.7386,
            'started_at' => now()->subMinutes(30),
            'completed_at' => now(),
            'status' => 'completed',
        ]);
        $gateway = PaymentGateway::create([
            'name' => 'Test Gateway',
            'provider' => 'stripe',
            'is_active' => true,
        ]);
        $currency = Currency::create([
            "name" => "JOR",
            "code" => "JOR"
        ]);
        $invoice = Invoice::create([
            'user_id' => 1,
            'trip_id' => $trip->id,
            'payment_gateway_id' => $gateway->id,
            'amount' => 25.00,
            'currency_id' => $currency ? $currency->id : 1,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        TripReview::create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'is_driver' => 0,
            'rating' => 3,
        ]);
        TripReview::create([
            'trip_id' => $trip->id,
            'user_id' => $driver->id,
            'is_driver' => 1,
            'rating' => 4,
        ]);
    }
}
