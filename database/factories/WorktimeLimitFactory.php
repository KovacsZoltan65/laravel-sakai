<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\WorktimeLimit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorktimeLimit>
 */
class WorktimeLimitFactory extends Factory
{
    protected $model = WorktimeLimit::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('2025-01-01', '2025-12-01');
        $end = (clone $start)->modify('+2 months');

        return [
            'name' => 'Limit - ' . $start->format('Y-m'),
            'company_id' => Company::factory(), // vagy fix ID pl. 1, ha nincs company factory
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'active' => $this->faker->boolean(80), // 80% eséllyel true
        ];
    }
}
