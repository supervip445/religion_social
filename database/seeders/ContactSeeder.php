<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'U Myo Myo',
                'email' => 'myomyo@example.com',
                'phone' => '09123456789',
                'subject' => 'Inquiry about Meditation Classes',
                'message' => 'Hello, I would like to know more about your meditation classes. What are the schedules and do I need to register in advance?',
                'is_read' => true,
            ],
            [
                'name' => 'Daw Khin Khin',
                'email' => 'khinkhin@example.com',
                'phone' => '09123456790',
                'subject' => 'Donation Information',
                'message' => 'I would like to make a donation to the monastery. Could you please provide information on how to donate?',
                'is_read' => true,
            ],
            [
                'name' => 'U Aung Aung',
                'email' => 'aungaung@example.com',
                'phone' => '09123456791',
                'subject' => 'Request for Dhamma Talk',
                'message' => 'I am interested in organizing a dhamma talk for our community group. Is it possible to arrange this?',
                'is_read' => false,
            ],
            [
                'name' => 'Daw Win Win',
                'email' => 'winwin@example.com',
                'phone' => null,
                'subject' => 'Volunteer Opportunities',
                'message' => 'I would like to volunteer at the monastery. What kind of volunteer work is available?',
                'is_read' => false,
            ],
            [
                'name' => 'U Htun Htun',
                'email' => 'htunhtun@example.com',
                'phone' => '09123456792',
                'subject' => 'Event Information',
                'message' => 'When is your next major event? I would like to attend with my family.',
                'is_read' => true,
            ],
            [
                'name' => 'Daw Nwe Nwe',
                'email' => 'nwenwe@example.com',
                'phone' => '09123456793',
                'subject' => 'Library Access',
                'message' => 'Can members of the public access your library? What are the opening hours?',
                'is_read' => false,
            ],
            [
                'name' => 'U Soe Soe',
                'email' => 'soesoe@example.com',
                'phone' => null,
                'subject' => 'General Inquiry',
                'message' => 'I am new to Buddhism and would like to learn more. Do you offer any beginner courses?',
                'is_read' => true,
            ],
            [
                'name' => 'Daw May May',
                'email' => 'maymay@example.com',
                'phone' => '09123456794',
                'subject' => 'Food Donation',
                'message' => 'I would like to donate food for the monks. What items are most needed and when can I bring them?',
                'is_read' => false,
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::create([
                'name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $contact['phone'],
                'subject' => $contact['subject'],
                'message' => $contact['message'],
                'is_read' => $contact['is_read'],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}

