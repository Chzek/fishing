<?php

namespace Database\Seeders;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnsureE2ETestUserSeeder extends Seeder
{
    public const TEST_EMAIL = 'test.playwright@fishinglogbook.local';
    public const TEST_PASSWORD = 'password';

    /**
     * Run the database seeds to ensure the Playwright E2E test user and linked angler exist.
     */
    public function run(): void
    {
        /** @var User $user */
        $user = User::firstOrCreate(
            ['email' => self::TEST_EMAIL],
            [
                'name' => 'Playwright Test User',
                'password' => Hash::make(self::TEST_PASSWORD),
                'type' => User::ADMIN_TYPE,
                'email_verified_at' => now(),
            ]
        );

        if (!Hash::check(self::TEST_PASSWORD, $user->password)) {
            $user->password = Hash::make(self::TEST_PASSWORD);
            $user->save();
        }

        /** @var Angler $angler */
        $angler = Angler::firstOrCreate(
            ['user_id' => $user->id],
            [
                'firstName' => 'Playwright',
                'lastName' => 'Tester',
                'middleName' => null,
            ]
        );

        if ($this->command) {
            $this->command->info("E2E Test user verified: {$user->email} (Angler ID: {$angler->id})");
        }
    }
}
