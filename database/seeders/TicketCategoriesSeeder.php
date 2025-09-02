<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketCategory;

class TicketCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'description' => 'Technical issues and bugs',
                'color' => '#dc3545',
                'sort_order' => 1
            ],
            [
                'name' => 'Account Issues',
                'slug' => 'account-issues',
                'description' => 'Account related problems',
                'color' => '#ffc107',
                'sort_order' => 2
            ],
            [
                'name' => 'Payment Issues',
                'slug' => 'payment-issues',
                'description' => 'Payment and billing problems',
                'color' => '#28a745',
                'sort_order' => 3
            ],
            [
                'name' => 'General Inquiry',
                'slug' => 'general-inquiry',
                'description' => 'General questions and inquiries',
                'color' => '#007bff',
                'sort_order' => 4
            ],
            [
                'name' => 'Feature Request',
                'slug' => 'feature-request',
                'description' => 'Request for new features',
                'color' => '#6f42c1',
                'sort_order' => 5
            ]
        ];

        foreach ($categories as $category) {
            TicketCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
