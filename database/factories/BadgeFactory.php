<?php

namespace Database\Factories;

use Fishinglog\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => 'test_badge_' . fake()->unique()->slug(),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['volume', 'species', 'diversity', 'cadence', 'conservation']),
            'tier' => fake()->randomElement(['bronze', 'silver', 'gold', 'platinum']),
            'points' => fake()->randomElement([10, 25, 50, 100]),
            'icon' => 'award',
            'image_path' => null,
            'rule_type' => 'total_catches',
            'rule_threshold' => 10,
            'accent_color' => 'amber',
            'sort_order' => 1,
            'sync_status' => 'pending_upstream',
        ];
    }
}
