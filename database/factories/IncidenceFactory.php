<?php

namespace Database\Factories;

use App\Models\Incidence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidenceFactory extends Factory
{
    protected $model = Incidence::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'user_id' => User::factory(),
            'assigned_to' => null,
        ];
    }
}
