<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['salesforce_org_id', 'status', 'triggered_by', 'started_at', 'finished_at', 'counts', 'error_message'])]
class SyncRun extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'counts' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function salesforceOrg(): BelongsTo
    {
        return $this->belongsTo(SalesforceOrg::class);
    }
}
