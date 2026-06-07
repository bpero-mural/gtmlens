<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['salesforce_org_id', 'external_key', 'metadata_type', 'api_name', 'label', 'parent_external_key', 'status', 'content_hash', 'attributes', 'first_seen_at', 'last_seen_at'])]
class MetadataEntity extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function salesforceOrg(): BelongsTo
    {
        return $this->belongsTo(SalesforceOrg::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MetadataEntityVersion::class);
    }

    public function searchDocument(): HasOne
    {
        return $this->hasOne(SearchDocument::class);
    }
}