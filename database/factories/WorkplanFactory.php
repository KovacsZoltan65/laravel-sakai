<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Workplan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workplan>
 */
class WorkplanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Workplan::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = (clone $startDate)->modify('+6 months');

        return [
            'name' => 'Munkaterv', // majd a seederből egészíted ki a sorszámmal
            'code' => strtoupper($this->faker->unique()->bothify('WP####')),
            'company_id' => null, // ezt a seederben adjuk meg
            'start_date' => null, // seeder számolja
            'end_date' => null,   // seeder számolja
            'active' => $this->faker->boolean(80),
        ];
    }
}
