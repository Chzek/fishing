<?php

namespace Fishinglog\Console\Commands;

use Fishinglog\Models\JournalPage;
use Fishinglog\Services\JournalTranscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class IngestJournalPagesCommand extends Command
{
    protected $signature = 'journal:ingest {--seed-sample : Import sample verified transcription for 2007 pages}';

    protected $description = 'Scan and catalog raw journal page photographs from storage/app/public/journals/raw.';

    public function handle(JournalTranscriptionService $transcriptionService): int
    {
        $this->info('Scanning storage/app/public/journals/raw for journal photographs...');

        $rawDir = storage_path('app/public/journals/raw');
        if (!File::isDirectory($rawDir)) {
            $this->error("Directory {$rawDir} does not exist.");
            return 1;
        }

        $files = File::glob("{$rawDir}/*.jpg");
        natsort($files);
        $totalFiles = count($files);

        $this->info("Found {$totalFiles} journal photograph(s).");

        $registered = 0;
        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $relativePath = "journals/raw/{$filename}";

            // Extract sequence or page number from digits in filename if possible
            preg_match('/(\d+)/', $filename, $matches);
            $num = !empty($matches[1]) ? (int) $matches[1] : null;

            JournalPage::firstOrCreate(
                ['filename' => $filename],
                [
                    'photo_path' => $relativePath,
                    'sequence_order' => $num ?? 1,
                    'is_processed' => false,
                ]
            );

            $registered++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully cataloged {$registered} journal page(s).");

        if ($this->option('seed-sample')) {
            $this->info('Seeding sample transcriptions for June/July 2007 expedition...');
            $samplePayload = $transcriptionService->getSample2007Payload();
            $entry = $transcriptionService->importParsedEntry($samplePayload);
            $this->info("Created sample journal entry: {$entry->title} with {$entry->pages()->count()} page(s).");
        }

        return 0;
    }
}
