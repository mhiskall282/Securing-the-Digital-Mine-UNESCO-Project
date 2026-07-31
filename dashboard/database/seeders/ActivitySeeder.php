<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $lead = User::where('email', 'lead@npontu.local')->first();

        $activities = [
            [
                'title' => 'Daily SMS count vs SMS count from logs',
                'description' => 'Compare the total SMS sent count on the platform dashboard against the count recorded in the application logs. Flag discrepancies greater than 0.5%.',
                'category' => 'Application',
                'recurrence' => 'daily',
            ],
            [
                'title' => 'API uptime check vs monitoring dashboard',
                'description' => 'Verify API uptime percentage reported by the monitoring tool against the actual response-time logs for all public endpoints.',
                'category' => 'Infrastructure',
                'recurrence' => 'daily',
            ],
            [
                'title' => 'Database backup verification',
                'description' => 'Confirm that automated database backups completed successfully and that the backup file size is within expected range.',
                'category' => 'Database',
                'recurrence' => 'daily',
            ],
            [
                'title' => 'Error rate review — application logs',
                'description' => 'Review application error logs for anomalies. Count 5xx errors and compare against the previous day baseline. Escalate if > 2x baseline.',
                'category' => 'Application',
                'recurrence' => 'daily',
            ],
            [
                'title' => 'Queue depth check — all active queues',
                'description' => 'Verify that all background job queues are draining normally. Alert if any queue has a backlog > 500 jobs.',
                'category' => 'Infrastructure',
                'recurrence' => 'daily',
            ],
            [
                'title' => 'SSL certificate expiry audit',
                'description' => 'Check SSL certificate expiry for all production domains. Alert if any certificate expires within 30 days.',
                'category' => 'Security',
                'recurrence' => 'daily',
            ],
        ];

        foreach ($activities as $data) {
            Activity::create([
                ...$data,
                'is_active' => true,
                'created_by' => $lead->id,
            ]);
        }
    }
}
