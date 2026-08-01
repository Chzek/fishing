<?php

namespace Database\Factories;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Record::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'anglers_id' => Angler::factory()->create()->id,
            'lakes_id' => Lake::factory()->create()->id,
            'fish_breeds_id' => FishBreed::factory()->create()->id,
            'lures_id' => Lure::factory()->create()->id,
            'weight' => $this->faker->randomFloat(2, 1, 100),
            'length' => $this->faker->randomFloat(2, 1, 100),
            'temperature' => $this->faker->randomFloat(0, 1, 150),
            'released' => $this->faker->boolean,
            'caught' => $this->faker->date('Y-m-d', 'now'),
        ];
    }
}
