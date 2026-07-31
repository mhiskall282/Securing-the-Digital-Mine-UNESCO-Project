<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only status change record.
 *
 * NEVER update rows in this table. Every status change is a new INSERT.
 * The "current" status for an activity on a date = the latest row by id.
 */
class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'date',
        'status',
        'remark',
        'updated_by',
        'actor_name',
        'actor_role',
        'actor_designation',
        'actor_ip',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
