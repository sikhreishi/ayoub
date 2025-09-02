<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;

class TicketSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'phone' => '1234567890'
            ]);
            $users = collect([$user]);
        }

        $tickets = [
            [
                'ticket_number' => 'TKT-' . str_pad(1, 6, '0', STR_PAD_LEFT),
                'title' => 'Login Issue',
                'description' => 'Unable to login to my account',
                'priority' => 'high',
                'status' => 'open',
                'ticket_category_id' => 1,
                'sender_type' => 'App\Models\User',
                'sender_id' => $users->first()->id,
                'sender_name' => $users->first()->name,
                'sender_email' => $users->first()->email,
                'sender_phone' => $users->first()->phone,
            ],
            [
                'ticket_number' => 'TKT-' . str_pad(2, 6, '0', STR_PAD_LEFT),
                'title' => 'Payment Problem',
                'description' => 'Payment was deducted but trip not confirmed',
                'priority' => 'urgent',
                'status' => 'pending',
                'ticket_category_id' => 2,
                'sender_type' => 'App\Models\User',
                'sender_id' => $users->first()->id,
                'sender_name' => $users->first()->name,
                'sender_email' => $users->first()->email,
                'sender_phone' => $users->first()->phone,
            ],
            [
                'ticket_number' => 'TKT-' . str_pad(3, 6, '0', STR_PAD_LEFT),
                'title' => 'App Crash',
                'description' => 'App crashes when trying to book a ride',
                'priority' => 'medium',
                'status' => 'in_progress',
                'ticket_category_id' => 3,
                'sender_type' => 'App\Models\User',
                'sender_id' => $users->first()->id,
                'sender_name' => $users->first()->name,
                'sender_email' => $users->first()->email,
                'sender_phone' => $users->first()->phone,
            ],
            [
                'ticket_number' => 'TKT-' . str_pad(4, 6, '0', STR_PAD_LEFT),
                'title' => 'Driver Complaint',
                'description' => 'Driver was rude and unprofessional',
                'priority' => 'low',
                'status' => 'resolved',
                'ticket_category_id' => 4,
                'sender_type' => 'App\Models\User',
                'sender_id' => $users->first()->id,
                'sender_name' => $users->first()->name,
                'sender_email' => $users->first()->email,
                'sender_phone' => $users->first()->phone,
                'resolved_at' => now()->subDays(1),
            ],
            [
                'ticket_number' => 'TKT-' . str_pad(5, 6, '0', STR_PAD_LEFT),
                'title' => 'Feature Request',
                'description' => 'Please add dark mode to the app',
                'priority' => 'low',
                'status' => 'closed',
                'ticket_category_id' => 5,
                'sender_type' => 'App\Models\User',
                'sender_id' => $users->first()->id,
                'sender_name' => $users->first()->name,
                'sender_email' => $users->first()->email,
                'sender_phone' => $users->first()->phone,
                'closed_at' => now()->subHours(2),
            ]
        ];

        foreach ($tickets as $ticketData) {
            Ticket::firstOrCreate(
                ['ticket_number' => $ticketData['ticket_number']],
                $ticketData
            );
        }
    }
}
