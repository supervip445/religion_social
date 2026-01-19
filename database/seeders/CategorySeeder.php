<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Dhamma Talks',
                'description' => 'Recordings and transcripts of dhamma talks given at the monastery',
                'slug' => 'dhamma-talks',
            ],
            [
                'name' => 'Events',
                'description' => 'Upcoming and past events at the monastery',
                'slug' => 'events',
            ],
            [
                'name' => 'Meditation',
                'description' => 'Articles and guides about meditation practice',
                'slug' => 'meditation',
            ],
            [
                'name' => 'News',
                'description' => 'Latest news and updates from the monastery',
                'slug' => 'news',
            ],
            [
                'name' => 'Announcements',
                'description' => 'Important announcements and notices',
                'slug' => 'announcements',
            ],
            [
                'name' => 'Activities',
                'description' => 'Monastery activities and programs',
                'slug' => 'activities',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'slug' => $category['slug'] ?? Str::slug($category['name']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

