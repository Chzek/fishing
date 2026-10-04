<?php

namespace Database\Factories;

use Fishinglog\Models\Expedition;
use Fishinglog\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-10 years', 'now');

        return [
            'expeditions_id' => Expedition::factory(),
            'entry_date' => $date->format('Y-m-d'),
            'start_date' => $date->format('Y-m-d'),
            'end_date' => $date->format('Y-m-d'),
            'title' => $this->faker->sentence(4),
            'body_markdown' => "# " . $this->faker->sentence() . "\n\n" . $this->faker->paragraphs(3, true),
            'highlights' => $this->faker->sentence(),
            'weather_summary' => '72°F, sunny and clear',
            'location_summary' => 'Catfish Creek',
            'template_type' => 'cabin_journal',
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
