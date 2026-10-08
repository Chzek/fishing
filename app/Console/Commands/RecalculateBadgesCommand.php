<?php

namespace Fishinglog\Console\Commands;

use Fishinglog\Models\Angler;
use Fishinglog\Services\BadgeEvaluatorService;
use Illuminate\Console\Command;

class RecalculateBadgesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badges:recalculate 
                            {--angler= : Specific Angler UUID or full name to recalculate} 
                            {--force : Clear existing earned badges before recalculating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate and recalculate merit badges across angler logbook catch histories';

    /**
     * Execute the console command.
     */
    public function handle(BadgeEvaluatorService $evaluator): int
    {
        $this->info('Starting merit badge recalculation...');

        $anglerParam = $this->option('angler');
        $force = (bool) $this->option('force');

        $specificAngler = null;
        if ($anglerParam) {
            $specificAngler = Angler::where('id', $anglerParam)
                ->orWhere('name', 'like', "%{$anglerParam}%")
                ->orWhere('lastName', 'like', "%{$anglerParam}%")
                ->first();

            if (!$specificAngler) {
                $this->error("Angler not found: {$anglerParam}");
                return self::FAILURE;
            }

            $this->line("Filtering to angler: {$specificAngler->full_name} ({$specificAngler->id})");
        }

        if ($force) {
            $this->warn('Warning: --force specified. Existing badges for selected angler(s) will be cleared.');
        }

        $result = $evaluator->recalculateAll($specificAngler, $force);

        $this->newLine();
        $this->info("Recalculation complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Anglers Processed', $result['anglers_processed']],
                ['Merit Badges Awarded', $result['badges_awarded']],
            ]
        );

        return self::SUCCESS;
    }
}
