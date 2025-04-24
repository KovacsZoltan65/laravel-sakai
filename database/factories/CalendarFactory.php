<?php

namespace Database\Factories;

use App\Models\Calendar;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarFactory extends Factory
{
    protected $model = Calendar::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'starts_at' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'ends_at' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'color' => $this->faker->safeColorName(),
        ];
    }
}