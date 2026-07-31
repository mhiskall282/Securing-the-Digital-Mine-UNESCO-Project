<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    protected $fillable = ['organization_id', 'name', 'api_token'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
