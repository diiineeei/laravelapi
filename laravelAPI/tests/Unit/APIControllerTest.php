<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Hour;
use App\Models\Employees;

class APIControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetEmployees()
    {
        // Cria alguns funcionários para testar
        Employees::factory()->count(100)->create();

        $response = $this->get('/api/employees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total',
                'per_page',
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'employees',
                        'matricula',
                        'tipo',
                        'data_admissao',
                        'hour_value',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }
}
