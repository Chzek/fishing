<?php

namespace Database\Factories;

use Fishinglog\Models\Expedition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpeditionFactory extends Factory
{
    protected $model = Expedition::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-10 years', 'now');
        $finish = (clone $start)->modify('+7 days');

        return [
            'description' => $this->faker->city() . ' Expedition ' . $start->format('Y'),
            'start' => $start,
            'finish' => $finish,
        ];
    }
}
