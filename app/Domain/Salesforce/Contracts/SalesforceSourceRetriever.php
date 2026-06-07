<?php

namespace App\Domain\Salesforce\Contracts;

interface SalesforceSourceRetriever
{
    /**
     * @return array<string, mixed>
     */
    public function retrieve(string $orgAlias, string $manifestPath, string $outputDir, int $waitMinutes): array;
}
