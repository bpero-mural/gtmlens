<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'alias', 'login_url', 'instance_url', 'api_version', 'status', 'connected_at'])]
class SalesforceOrg extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'connected_at' => 'datetime',
        ];
    }

    public function syncRuns(): HasMany
    {
        return $this->hasMany(SyncRun::class);
    }
}
