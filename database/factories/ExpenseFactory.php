<?php

namespace Database\Factories;

use App\Models\Colocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'colocation_id' => Colocation::factory(),
            'category_id' => null,
            'payer_id' => User::factory(),
            'title' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 5, 500),
            'expense_date' => fake()->date(),
        ];
    }
}
