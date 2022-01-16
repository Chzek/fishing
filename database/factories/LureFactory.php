<?php

namespace Database\Factories;

use Fishinglog\Lure;
use Illuminate\Database\Eloquent\Factories\Factory;

class LureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lure::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'fake-'. $this->faker->company,
            'color' => $this->faker->colorName,
            'size' => $this->faker->numerify('##.## oz.'),
        ];
    }
}
