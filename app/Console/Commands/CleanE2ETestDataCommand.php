<?php

namespace Fishinglog\Console\Commands;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Photo;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Console\Command;

class CleanE2ETestDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:clean-e2e-data {--email=test.playwright@fishinglogbook.local : Email of the E2E test user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up catches and test records created by the E2E Playwright test user.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->option('email');
        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->info("E2E test user with email '{$email}' does not exist. No records to clean.");
            return 0;
        }

        $angler = $user->angler;
        if (!$angler) {
            $this->info("No angler associated with user '{$email}'.");
            return 0;
        }

        // Find all records belonging to this test angler (including soft-deleted)
        $records = Record::withTrashed()
            ->where('anglers_id', $angler->id)
            ->get();

        $count = $records->count();
        if ($count === 0) {
            $this->info("No test records found for {$user->name} ({$angler->full_name}).");
            return 0;
        }

        $recordIds = $records->pluck('id')->toArray();

        // Remove any attached photos
        Photo::where('photoable_type', Record::class)
            ->whereIn('photoable_id', $recordIds)
            ->delete();

        // Permanently delete the test records
        Record::withTrashed()
            ->whereIn('id', $recordIds)
            ->forceDelete();

        $this->info("Successfully purged {$count} E2E test catch record(s) logged by {$angler->full_name}.");
        return 0;
    }
}
