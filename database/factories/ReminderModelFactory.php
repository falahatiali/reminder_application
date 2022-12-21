<?php

namespace Database\Factories;

use App\Models\ReminderModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReminderModel>
 */
class ReminderModelFactory extends Factory
{
    protected $model = ReminderModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'frontend' => fake()->sentence(3),
            'backend' => fake()->sentence(3),
            'body' => fake()->sentence(3),
            'additional_text' => fake()->sentence(3),
            'reminder_type' => fake()->sentence(1),
            'frequency' => 'EveryMinute',
            'day' => 12,
            'date' => random_int(1, 31),
            'time' => '12:12',
            'expression' => '* * * * *',
            'run_once' => 1,
            'active' => 1,
            'is_complete' => 1,
            'user_id' => User::factory()->create()->id
        ];
    }
}
