<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['metadata_entity_id', 'sync_run_id', 'version_number', 'content_hash', 'payload_path', 'normalized_payload'])]
class MetadataEntityVersion extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'normalized_payload' => 'array',
        ];
    }

    public function metadataEntity(): BelongsTo
    {
        return $this->belongsTo(MetadataEntity::class);
    }

    public function syncRun(): BelongsTo
    {
        return $this->belongsTo(SyncRun::class);
    }
}