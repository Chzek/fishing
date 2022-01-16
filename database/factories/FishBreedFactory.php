<?php

namespace Database\Factories;

use Fishinglog\FishBreed;
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
            'name' => 'fake-'.$this->faker->name,
            'fish_families_id' => function(){
                return \Fishinglog\FishFamily::factory()->create()->id;
            },
        ];
    }
}
