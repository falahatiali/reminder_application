<?php

namespace Database\Factories;

use App\Models\TelegramModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TelegramModel>
 */
class TelegramModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition()
    {
        return [
            'type' => TelegramModel::TYPE['MESSAGE'],
            'from_id' => $id = random_int(1000000, 5000000),
            'message_id' => fake()->numberBetween(10030020, 213333331),
            'chat_instance' => fake()->uuid(),
            'data' => fake()->uuid,
            'is_bot' => false,
            'first_name' => fake()->firstName,
            'username' => fake()->userName,
            'language_code' => 'en',
            'chat_id' => $id,
            'chat_type' => 'chat',
            'unix_timestamp' => fake()->unixTime,
            'text' => fake()->sentence(3),
            'user_id' => User::factory()->create()->id,
            'telegram' => [],
            'reminder_type' => fake()->randomElement(['create_new',
                'front',
                'backend',
                'body',
                'additional_text',
                'frequency',
                'general',
                'old']),
        ];
    }
}
