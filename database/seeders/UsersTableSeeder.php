<?php

namespace Database\Seeders;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Models\User;
use App\Services\WalletService;
use App\Settings\AppSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin account
        $superAdmin = $this->createUser(UserType::SuperAdmin, 'Super Admin', UserType::SuperAdmin->getUsername(), UserType::SuperAdmin->getPhone());
        if (class_exists(WalletService::class) && class_exists(TransactionName::class)) {
            (new WalletService)->deposit($superAdmin, 10 * 100_000, TransactionName::CapitalDeposit);
        }

        // Create User account
        $user = $this->createUser(UserType::User, 'User', UserType::User->getUsername(), UserType::User->getPhone());
        if (class_exists(WalletService::class) && class_exists(TransactionName::class)) {
            (new WalletService)->deposit($user, 1 * 100_000, TransactionName::CapitalDeposit);
        }

        // Create additional test user accounts
        $user1 = $this->createUser(UserType::User, 'User 1', 'user001', '09111111111');
        $user2 = $this->createUser(UserType::User, 'User 2', 'user002', '09111111112');
        $user3 = $this->createUser(UserType::User, 'User 3', 'user003', '09111111113');
    }

    private function createUser(UserType $type, $name, $user_name, $phone)
    {
        return User::create([
            'name' => $name,
            'user_name' => $user_name,
            'phone' => $phone,
            'password' => Hash::make('dhammacenter'),
            'status' => 1,
            'is_changed_password' => 1,
            'type' => $type->value,
        ]);
    }
}
