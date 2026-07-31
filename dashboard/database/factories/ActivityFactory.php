<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    public function definition(): array
    {
        $titles = [
            'Daily SMS count vs SMS count from logs',
            'API uptime check vs monitoring dashboard',
            'Database backup verification',
            'Network latency baseline check',
            'Error rate review — application logs',
            'CDN cache hit ratio verification',
            'Queue depth check — all active queues',
            'SSL certificate expiry audit',
            'Disk usage report — production servers',
            'Memory utilisation review',
        ];

        return [
            'title' => fake()->randomElement($titles),
            'description' => fake()->sentence(12),
            'category' => fake()->randomElement(['Infrastructure', 'Application', 'Database', 'Network', 'Security']),
            'recurrence' => fake()->randomElement(['daily', 'adhoc']),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function daily(): static
    {
        return $this->state(['recurrence' => 'daily']);
    }
}
