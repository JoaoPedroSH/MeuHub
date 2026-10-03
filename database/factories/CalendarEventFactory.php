<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarEventFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'title' => fake()->sentence(3), 'starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour(), 'color' => '#6366f1'];
    }
}
