<?php

namespace Tests\Feature;

use Database\Seeders\EnsureE2ETestUserSeeder;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CleanE2ETestDataTest extends TestCase
{
    use DatabaseTransactions;

    public function test_ensure_e2e_test_user_seeder_creates_user_and_angler(): void
    {
        $this->seed(EnsureE2ETestUserSeeder::class);

        $user = User::where('email', EnsureE2ETestUserSeeder::TEST_EMAIL)->first();
        $this->assertNotNull($user);
        $this->assertEquals('Playwright Test User', $user->name);
        $this->assertTrue($user->isAdmin());

        $angler = $user->angler;
        $this->assertNotNull($angler);
        $this->assertEquals('Playwright', $angler->firstName);
        $this->assertEquals('Tester', $angler->lastName);
    }

    public function test_clean_e2e_data_command_purges_test_catches(): void
    {
        $this->seed(EnsureE2ETestUserSeeder::class);
        $user = User::where('email', EnsureE2ETestUserSeeder::TEST_EMAIL)->firstOrFail();
        $angler = $user->angler;
        $this->assertNotNull($angler);

        $family = FishFamily::firstOrCreate(['name' => 'Salmonidae']);
        $breed = FishBreed::firstOrCreate([
            'name' => 'Atlantic Salmon',
            'fish_families_id' => $family->id,
        ]);
        $lake = Lake::firstOrCreate(['name' => 'Agnew Lake']);

        // Create dummy test record
        $record = Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 19.5,
            'caught' => now()->toDateString(),
            'released' => true,
        ]);

        $this->assertDatabaseHas('records', ['id' => $record->id]);

        // Run the cleanup command
        $this->artisan('test:clean-e2e-data')
            ->expectsOutputToContain('Successfully purged 1 E2E test catch record')
            ->assertSuccessful();

        $this->assertDatabaseMissing('records', ['id' => $record->id]);
    }
}
