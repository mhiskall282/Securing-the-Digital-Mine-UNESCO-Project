<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'activity_id' => Activity::factory(),
            'date' => fake()->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['pending', 'done']),
            'remark' => fake()->sentence(8),
            'updated_by' => $user->id,
            'actor_name' => $user->name,
            'actor_role' => $user->role,
            'actor_designation' => $user->designation,
            'actor_ip' => fake()->ipv4(),
        ];
    }

    public function done(): static
    {
        return $this->state(['status' => 'done', 'remark' => 'Verified and resolved.']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function forDate(string $date): static
    {
        return $this->state(['date' => $date]);
    }
}
