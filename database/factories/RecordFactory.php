<?php

namespace Database\Factories;

use Fishinglog\Record;
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
            'anglers_id' => \Fishinglog\Angler::factory()->create()->id,
            'lakes_id' => \Fishinglog\Lake::factory()->create()->id,
            'fish_breeds_id' => \Fishinglog\FishBreed::factory()->create()->id,
            'lures_id' => \Fishinglog\Lure::factory()->create()->id,
            'weight' => $this->faker->randomFloat(2, 1, 100),
            'length' => $this->faker->randomFloat(2, 1, 100),
            'temperature' => $this->faker->randomFloat(0, 1, 150),
            'released' => $this->faker->boolean,
            'caught' => $this->faker->date('Y-m-d', 'now'),
        ];
    }
}
