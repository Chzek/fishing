<?php

namespace Database\Factories;

use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\JournalPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalPageFactory extends Factory
{
    protected $model = JournalPage::class;

    public function definition(): array
    {
        $num = $this->faker->numberBetween(1300, 1600);

        return [
            'journal_entry_id' => JournalEntry::factory(),
            'page_number' => $this->faker->numberBetween(1, 100),
            'sequence_order' => 1,
            'filename' => "{$num}.jpg",
            'photo_path' => "journals/raw/{$num}.jpg",
            'raw_ocr_text' => $this->faker->paragraphs(2, true),
            'structured_metadata' => [
                'weather' => '78°F and cloudy',
                'crew' => ['Joe Mroczek', 'Dave', 'Andy'],
            ],
            'is_processed' => true,
        ];
    }
}
