<?php

namespace Database\Seeders;

use App\Models\MonasteryBuildingDonation;
use Illuminate\Database\Seeder;

class MonasteryBuildingDonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donations = [
            [
                'donor_name' => 'U Maung Maung',
                'amount' => 300000.00,
                'donation_purpose' => 'Meditation Hall Construction',
                'date' => '2025-03-15',
                'description' => 'Donation for the construction of the new meditation hall. This generous contribution will help create a peaceful space for practitioners.',
            ],
            [
                'donor_name' => 'Daw Aye Aye',
                'amount' => 200000.00,
                'donation_purpose' => 'Library Building',
                'date' => '2025-03-20',
                'description' => 'Donation for building a new library to house Buddhist texts and scriptures.',
            ],
            [
                'donor_name' => 'U Kyaw Kyaw',
                'amount' => 500000.00,
                'donation_purpose' => 'Main Hall Renovation',
                'date' => '2025-04-01',
                'description' => 'Major donation for renovating the main hall of the monastery.',
            ],
            [
                'donor_name' => 'Daw Hla Hla',
                'amount' => 150000.00,
                'donation_purpose' => 'Monks\' Quarters',
                'date' => '2025-04-10',
                'description' => 'Donation for building new quarters for the resident monks.',
            ],
            [
                'donor_name' => 'U Min Min',
                'amount' => 250000.00,
                'donation_purpose' => 'Kitchen and Dining Hall',
                'date' => '2025-04-15',
                'description' => 'Donation for constructing a new kitchen and dining hall facility.',
            ],
            [
                'donor_name' => 'Daw Su Su',
                'amount' => 100000.00,
                'donation_purpose' => 'Bathroom Facilities',
                'date' => '2025-04-20',
                'description' => 'Donation for improving bathroom and sanitation facilities.',
            ],
            [
                'donor_name' => 'U Zaw Zaw',
                'amount' => 400000.00,
                'donation_purpose' => 'Main Gate and Fence',
                'date' => '2025-05-01',
                'description' => 'Donation for constructing the main gate and perimeter fence.',
            ],
            [
                'donor_name' => 'Daw Mya Mya',
                'amount' => 180000.00,
                'donation_purpose' => 'Garden and Landscaping',
                'date' => '2025-05-05',
                'description' => 'Donation for creating a beautiful garden and landscaping around the monastery.',
            ],
        ];

        foreach ($donations as $donation) {
            MonasteryBuildingDonation::create([
                'donor_name' => $donation['donor_name'],
                'amount' => $donation['amount'],
                'donation_purpose' => $donation['donation_purpose'],
                'date' => $donation['date'],
                'description' => $donation['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

