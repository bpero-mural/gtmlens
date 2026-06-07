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
            new MetadataProbeQuery('field_definitions', 'SELECT DurableId, QualifiedApiName, Label, DataType FROM FieldDefinition LIMIT 200'),
            new MetadataProbeQuery('apex_classes', 'SELECT Id, Name, Status, ApiVersion FROM ApexClass LIMIT 200', true),
            new MetadataProbeQuery('apex_triggers', 'SELECT Id, Name, Status, ApiVersion, TableEnumOrId FROM ApexTrigger LIMIT 200', true),
            new MetadataProbeQuery('validation_rules', 'SELECT Id, ValidationName, Active, Description, ErrorConditionFormula, ErrorMessage FROM ValidationRule LIMIT 200', true),
            new MetadataProbeQuery('flows', 'SELECT Id, DeveloperName, MasterLabel, Status, ProcessType FROM Flow LIMIT 200', true),
            new MetadataProbeQuery('flow_definitions', 'SELECT Id, DeveloperName, ActiveVersionId, LatestVersionId FROM FlowDefinition LIMIT 200', true),
            new MetadataProbeQuery('flow_version_views', 'SELECT Id, DurableId, ApiName, Label FROM FlowVersionView LIMIT 200', true),
            new MetadataProbeQuery('picklist_value_infos', 'SELECT Id, EntityParticleId, Value, IsActive FROM PicklistValueInfo LIMIT 200'),
        ];
    }
}
