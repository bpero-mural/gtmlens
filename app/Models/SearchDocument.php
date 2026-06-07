<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['metadata_entity_id', 'title', 'api_name', 'metadata_type', 'content', 'filters'])]
class SearchDocument extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'filters' => 'array',
        ];
    }

    public function metadataEntity(): BelongsTo
    {
        return $this->belongsTo(MetadataEntity::class);
    }
}