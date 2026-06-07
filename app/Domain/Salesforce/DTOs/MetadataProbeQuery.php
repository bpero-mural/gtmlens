<?php

namespace App\Domain\Salesforce\DTOs;

class MetadataProbeQuery
{
    public function __construct(
        public readonly string $key,
        public readonly string $soql,
        public readonly bool $usesToolingApi = false,
    ) {
    }
}
