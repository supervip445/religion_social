<?php

namespace Database\Seeders;

use App\Models\BannerText;
use App\Models\User;
use Illuminate\Database\Seeder;

class BannerTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get SuperAdmin user
        $admin = User::where('type', 'super_admin')->first();
        
        if (!$admin) {
            $this->command->error('SuperAdmin user not found. Please run UsersTableSeeder first.');
            return;
        }

        $bannerTexts = [
            [
                'text' => 'Welcome to မဟာဝိဇိတာရာမတိုက် Dhamma Center Monastery - A place of peace, wisdom, and compassion',
                'is_active' => true,
            ],
            [
                'text' => 'Join us for daily meditation sessions and weekly Dhamma talks - All are welcome',
                'is_active' => true,
            ],
            [
                'text' => 'Support our monastery through donations - Your generosity helps spread the teachings of the Buddha',
                'is_active' => true,
            ],
            [
                'text' => 'New Year Blessing Ceremony coming soon - Stay tuned for updates',
                'is_active' => false,
            ],
            [
                'text' => 'Weekly meditation schedule: Morning 6:00 AM - 7:00 AM, Evening 6:00 PM - 7:00 PM',
                'is_active' => true,
            ],
        ];

        foreach ($bannerTexts as $bannerText) {
            BannerText::create([
                'text' => $bannerText['text'],
                'is_active' => $bannerText['is_active'],
                'admin_id' => $admin->id,
            ]);
        }

        $this->command->info('Banner text seeding completed!');
        $this->command->info('Created ' . count($bannerTexts) . ' banner texts.');
    }
}

