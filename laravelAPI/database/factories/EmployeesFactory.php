<?php

namespace Database\Factories;

use App\Models\Employees;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employees>
 */
class EmployeesFactory extends Factory
{
    protected $model = Employees::class;

    public function definition()
    {
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'employees' => $this->faker->name,
            'matricula' => $this->faker->unique()->randomNumber(),
            'tipo' => $this->faker->randomElement(['CLT', 'PJ']),
            'data_admissao' => $this->faker->date(),
        ];
    }
}
