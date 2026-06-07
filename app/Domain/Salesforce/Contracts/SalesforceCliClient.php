<?php

namespace App\Domain\Salesforce\Contracts;

use App\Domain\Salesforce\DTOs\MetadataProbeQuery;

interface SalesforceCliClient
{
    /**
     * @return array<string, mixed>
     */
    public function query(string $orgAlias, MetadataProbeQuery $query): array;
}
