<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['salesforce_org_id', 'sync_run_id', 'snapshot_type', 'path', 'content_hash', 'metadata'])]
class MetadataSnapshot extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }
}
