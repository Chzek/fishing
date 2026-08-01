<?php

namespace Database\Factories;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

class FishBreedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FishBreed::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'fake-' . $this->faker->name,
            'fish_families_id' => function () {
                return FishFamily::factory()->create()->id;
            },
        ];
    }
}
