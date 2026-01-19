<?php

namespace Database\Seeders;

use App\Models\Donation;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donations = [
            [
                'donor_name' => 'ဦးမောင်မောင်',
                'amount' => 300000.00,
                'donation_type' => 'ကျောင်းတိုက် ဆောက်လုပ်ရေး လှူဒါန်းမှု',
                'date' => '2025-03-15',
                'status' => 'approved',
                'notes' => 'တရားထိုင်ခန်းသစ် ဆောက်လုပ်ရန် လှူဒါန်းမှု',
            ],
            [
                'donor_name' => 'ဒေါ်အေးအေး',
                'amount' => 50000.00,
                'donation_type' => 'စာအုပ် လှူဒါန်းမှု',
                'date' => '2025-04-10',
                'status' => 'approved',
                'notes' => 'စာကြည့်တိုက်အတွက် ဗုဒ္ဓဘာသာစာအုပ်များ လှူဒါန်းမှု',
            ],
            [
                'donor_name' => 'ဦးကျော်ကျော်',
                'amount' => 100000.00,
                'donation_type' => 'ဆွမ်းလှူဒါန်းမှု',
                'date' => '2025-04-20',
                'status' => 'approved',
                'notes' => 'ရဟန်းတော်များအတွက် လစဉ် ဆွမ်းလှူဒါန်းမှု',
            ],
            [
                'donor_name' => 'ဒေါ်လှလှ',
                'amount' => 75000.00,
                'donation_type' => 'အထွေထွေ လှူဒါန်းမှု',
                'date' => '2025-05-01',
                'status' => 'pending',
                'notes' => null,
            ],
            [
                'donor_name' => 'ဦ်းမင်း',
                'amount' => 200000.00,
                'donation_type' => 'ကျောင်းတိုက် ဆောက်လုပ်ရေး လှူဒါန်းမှု',
                'date' => '2025-05-05',
                'status' => 'approved',
                'notes' => 'အဆောက်အဦး ပြန်လည်မွမ်းမံရန် လှူဒါန်းမှု',
            ],
            [
                'donor_name' => 'ဒေါ်စုစု',
                'amount' => 25000.00,
                'donation_type' => 'ပန်းလှူဒါန်းမှု',
                'date' => '2025-05-10',
                'status' => 'approved',
                'notes' => 'ဗုဒ္ဓရုပ်ပွားတော်များအတွက် အပတ်စဉ် ပန်းလှူဒါန်းမှု',
            ],
            [
                'donor_name' => 'ဦးဇော်ဇော်',
                'amount' => 150000.00,
                'donation_type' => 'အထွေထွေ လှူဒါန်းမှု',
                'date' => '2025-05-15',
                'status' => 'pending',
                'notes' => null,
            ],
            [
                'donor_name' => 'ဒေါ်မြမြ',
                'amount' => 60000.00,
                'donation_type' => 'ဆွမ်းလှူဒါန်းမှု',
                'date' => '2025-05-20',
                'status' => 'approved',
                'notes' => 'ပွဲအတွက် အထူး ဆွမ်းလှူဒါန်းမှု',
            ],
        ];

        foreach ($donations as $donation) {
            Donation::create([
                'donor_name' => $donation['donor_name'],
                'amount' => $donation['amount'],
                'donation_type' => $donation['donation_type'],
                'date' => $donation['date'],
                'status' => $donation['status'],
                'notes' => $donation['notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

