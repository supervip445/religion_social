<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run(): void
    {

        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            PaymentTypeTableSeeder::class,
            UserPaymentTableSeeder::class,
            // Dhamma Center Seeders
            CategorySeeder::class,
            PostSeeder::class,
            DhammaSeeder::class,
            DonationSeeder::class,
            BiographySeeder::class,
            //MonasterySeeder::class,
            MonasteryBuildingDonationSeeder::class,
            ContactSeeder::class,
            BannerSeeder::class,
            BannerTextSeeder::class,
            AcademicYearsTableSeeder::class,
            ClassesTableSeeder::class,
            SubjectsTableSeeder::class,
        ]);

    }
}
