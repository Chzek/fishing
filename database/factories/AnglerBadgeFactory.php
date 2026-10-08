<?php

namespace Database\Factories;

use Fishinglog\Models\Angler;
use Fishinglog\Models\AnglerBadge;
use Fishinglog\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnglerBadge>
 */
class AnglerBadgeFactory extends Factory
{
    protected $model = AnglerBadge::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anglers_id' => Angler::factory(),
            'badge_id' => Badge::factory(),
            'record_id' => null,
            'expedition_id' => null,
            'awarded_at' => now(),
            'points' => 25,
            'trigger_summary' => fake()->sentence(),
            'sync_status' => 'pending_upstream',
        ];
    }
}
