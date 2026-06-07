<?php

namespace App\Domain\Salesforce\Services;

use App\Domain\Salesforce\DTOs\MetadataProbeQuery;

class MetadataProbeQueryCatalog
{
    /**
     * @return array<int, MetadataProbeQuery>
     */
    public function all(): array
    {
        return [
            new MetadataProbeQuery('entity_definitions', 'SELECT DurableId, QualifiedApiName, Label FROM EntityDefinition LIMIT 200'),
            new MetadataProbeQuery('field_definitions', "SELECT DurableId, QualifiedApiName, Label, DataType FROM FieldDefinition WHERE EntityDefinition.QualifiedApiName = 'User' LIMIT 200"),
            new MetadataProbeQuery('apex_classes', 'SELECT Id, Name, Status, ApiVersion FROM ApexClass LIMIT 200', true),
            new MetadataProbeQuery('apex_triggers', 'SELECT Id, Name, Status, ApiVersion, TableEnumOrId FROM ApexTrigger LIMIT 200', true),
            new MetadataProbeQuery('validation_rules', 'SELECT Id, ValidationName, Active, Description FROM ValidationRule LIMIT 200', true),
            new MetadataProbeQuery('flows', 'SELECT Id, MasterLabel, Status, ProcessType FROM Flow LIMIT 200', true),
            new MetadataProbeQuery('flow_definitions', 'SELECT Id, ActiveVersionId, LatestVersionId FROM FlowDefinition LIMIT 200', true),
            new MetadataProbeQuery('flow_version_views', "SELECT Id, DurableId, FlowDefinitionViewId, Label, Status, VersionNumber, ProcessType FROM FlowVersionView WHERE DurableId = 'gtm_lens_probe_noop' LIMIT 200"),
            new MetadataProbeQuery('picklist_value_infos', "SELECT Id, DurableId, Value, IsActive FROM PicklistValueInfo WHERE EntityParticleId = 'User.LanguageLocaleKey' LIMIT 200"),
        ];
    }
}
