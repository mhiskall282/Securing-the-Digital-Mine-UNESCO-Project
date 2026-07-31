<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $activities = Activity::all();
        $users = User::all();

        // Seed 7 days of realistic activity log data
        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = Carbon::today()->subDays($daysAgo)->format('Y-m-d');

            foreach ($activities as $activity) {
                // Each activity starts as pending at the beginning of each day
                // For past days, most are resolved; for today, some still pending
                $actor = $users->random();

                if ($daysAgo > 0) {
                    // Past day: all done
                    ActivityLog::create([
                        'activity_id' => $activity->id,
                        'date' => $date,
                        'status' => 'done',
                        'remark' => fake()->randomElement([
                            'Verified — all counts match within tolerance.',
                            'Checked and confirmed. No anomalies.',
                            'Completed. Backup file size within expected range.',
                            'Reviewed. Error rate within baseline.',
                            'Checked. Queue draining normally.',
                            'Confirmed. Certificate valid for 90+ days.',
                        ]),
                        'updated_by' => $actor->id,
                        'actor_name' => $actor->name,
                        'actor_role' => $actor->role,
                        'actor_designation' => $actor->designation,
                        'actor_ip' => '127.0.0.1',
                    ]);
                } else {
                    // Today: some done, some still pending (realistic handover scenario)
                    if (rand(0, 2) > 0) { // ~67% chance of done
                        ActivityLog::create([
                            'activity_id' => $activity->id,
                            'date' => $date,
                            'status' => 'done',
                            'remark' => 'Completed during morning shift.',
                            'updated_by' => $actor->id,
                            'actor_name' => $actor->name,
                            'actor_role' => $actor->role,
                            'actor_designation' => $actor->designation,
                            'actor_ip' => '127.0.0.1',
                        ]);
                    }
                    // Remaining activities stay at 'pending' (no log entry = pending default)
                }
            }
        }
    }
}
