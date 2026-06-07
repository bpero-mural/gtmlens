<?php

namespace Tests\Unit;

use App\Domain\Salesforce\Guards\SalesforceQueryGuard;
use App\Domain\Salesforce\Services\MetadataProbeQueryCatalog;
use PHPUnit\Framework\TestCase;

class MetadataProbeQueryCatalogTest extends TestCase
{
    public function test_catalog_queries_stay_allowlisted_and_real_org_safe(): void
    {
        $guard = new SalesforceQueryGuard();
        $queries = collect((new MetadataProbeQueryCatalog())->all())->keyBy('key');

        foreach ($queries as $query) {
            $guard->assertAllowed($query->soql);
            $this->assertStringNotContainsString('SELECT *', strtoupper($query->soql));
        }

        $this->assertStringContainsString("WHERE EntityDefinition.QualifiedApiName = 'User'", $queries->get('field_definitions')->soql);
        $this->assertStringNotContainsString('ErrorConditionFormula', $queries->get('validation_rules')->soql);
        $this->assertStringNotContainsString('ErrorMessage', $queries->get('validation_rules')->soql);
        $this->assertStringNotContainsString('DeveloperName', $queries->get('flows')->soql);
        $this->assertStringNotContainsString('DeveloperName', $queries->get('flow_definitions')->soql);
        $this->assertFalse($queries->get('flow_version_views')->usesToolingApi);
        $this->assertStringContainsString("WHERE DurableId = 'gtm_lens_probe_noop'", $queries->get('flow_version_views')->soql);
        $this->assertStringContainsString("WHERE EntityParticleId = 'User.LanguageLocaleKey'", $queries->get('picklist_value_infos')->soql);
    }
}
