<?php

namespace Database\Factories;

use App\Models\Hour;
use App\Models\Employees;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hour>
 */
class HourFactory extends Factory
{
    protected $model = Hour::class;

    public function definition()
    {
        return [
            'year' => $this->faker->year,
            'month' => $this->faker->month,
            'total_hours' => $this->faker->numberBetween(1, 40),
            'employees_id' => function () {
                return Employees::factory()->create()->id;
            },
        ];
    }
}
