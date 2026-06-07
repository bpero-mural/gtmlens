<?php

namespace Tests\Feature;

use App\Models\MetadataEntity;
use App\Models\MetadataEntityVersion;
use App\Models\MetadataSnapshot;
use App\Models\SalesforceOrg;
use App\Models\SearchDocument;
use App\Models\SyncRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SalesforceSourceNormalizeCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(storage_path('app/snapshots/normalize-test'));
    }

    public function test_it_requires_a_source_retrieve_snapshot(): void
    {
        $this->artisan('salesforce:source-normalize')
            ->assertFailed()
            ->expectsOutputToContain('No source retrieve snapshot was found');

        $this->assertDatabaseCount('sync_runs', 0);
    }

    public function test_it_records_failed_run_when_source_directory_is_missing(): void
    {
        $org = SalesforceOrg::query()->create([
            'name' => 'normalize-test',
            'alias' => 'normalize-test',
            'status' => 'cli_placeholder',
        ]);
        $sourceRun = SyncRun::query()->create([
            'salesforce_org_id' => $org->id,
            'status' => 'completed',
            'triggered_by' => 'cli_source_retrieve',
        ]);
        MetadataSnapshot::query()->create([
            'salesforce_org_id' => $org->id,
            'sync_run_id' => $sourceRun->id,
            'snapshot_type' => 'source_retrieve',
            'path' => 'snapshots/normalize-test/missing/source',
        ]);

        $this->artisan('salesforce:source-normalize '.$sourceRun->id)
            ->assertFailed()
            ->expectsOutputToContain('Source snapshot directory was not found');

        $normalizationRun = SyncRun::query()->where('triggered_by', 'source_normalization')->firstOrFail();

        $this->assertSame('failed', $normalizationRun->status);
    }

    public function test_it_normalizes_retrieved_source_into_metadata_tables(): void
    {
        [$org, $sourceRun] = $this->createSourceSnapshotFixture();

        $this->artisan('salesforce:source-normalize '.$sourceRun->id)
            ->assertSuccessful();

        $normalizationRun = SyncRun::query()->where('triggered_by', 'source_normalization')->firstOrFail();

        $this->assertSame('completed', $normalizationRun->status);
        $this->assertSame([
            'ApexClass' => 1,
            'ApexTrigger' => 1,
            'CustomField' => 1,
            'CustomObject' => 1,
            'Flow' => 1,
            'ValidationRule' => 1,
        ], $normalizationRun->counts);

        $this->assertDatabaseCount('metadata_entities', 6);
        $this->assertDatabaseCount('metadata_entity_versions', 6);
        $this->assertDatabaseCount('search_documents', 6);

        $object = MetadataEntity::query()->where('external_key', 'CustomObject:Widget__c')->firstOrFail();
        $field = MetadataEntity::query()->where('external_key', 'CustomField:Widget__c.Score__c')->firstOrFail();
        $rule = MetadataEntity::query()->where('external_key', 'ValidationRule:Widget__c.Score_Required')->firstOrFail();
        $flow = MetadataEntity::query()->where('external_key', 'Flow:Widget_After_Save')->firstOrFail();
        $class = MetadataEntity::query()->where('external_key', 'ApexClass:WidgetService')->firstOrFail();

        $this->assertSame($org->id, $object->salesforce_org_id);
        $this->assertSame('Widget', $object->label);
        $this->assertSame('CustomObject:Widget__c', $field->parent_external_key);
        $this->assertSame('Number', $field->attributes['type']);
        $this->assertSame('CustomObject:Widget__c', $rule->parent_external_key);
        $this->assertSame('inactive', $rule->status);
        $this->assertSame('AutoLaunchedFlow', $flow->attributes['process_type']);
        $this->assertSame('Active', $class->status);

        $version = MetadataEntityVersion::query()->where('metadata_entity_id', $field->id)->firstOrFail();
        $this->assertSame('snapshots/normalize-test/'.$sourceRun->id.'/source/objects/Widget__c/fields/Score__c.field-meta.xml', $version->payload_path);

        $searchDocument = SearchDocument::query()->where('metadata_entity_id', $field->id)->firstOrFail();
        $this->assertStringContainsString('Widget__c.Score__c', $searchDocument->content);
    }

    public function test_it_does_not_create_duplicate_versions_for_unchanged_content(): void
    {
        [, $sourceRun] = $this->createSourceSnapshotFixture();

        $this->artisan('salesforce:source-normalize '.$sourceRun->id)->assertSuccessful();
        $this->artisan('salesforce:source-normalize '.$sourceRun->id)->assertSuccessful();

        $this->assertDatabaseCount('metadata_entities', 6);
        $this->assertDatabaseCount('metadata_entity_versions', 6);
        $this->assertDatabaseCount('search_documents', 6);
    }

    /**
     * @return array{0: SalesforceOrg, 1: SyncRun}
     */
    private function createSourceSnapshotFixture(): array
    {
        $org = SalesforceOrg::query()->create([
            'name' => 'normalize-test',
            'alias' => 'normalize-test',
            'status' => 'cli_placeholder',
        ]);
        $sourceRun = SyncRun::query()->create([
            'salesforce_org_id' => $org->id,
            'status' => 'completed',
            'triggered_by' => 'cli_source_retrieve',
        ]);
        $basePath = 'snapshots/normalize-test/'.$sourceRun->id.'/source';
        $root = storage_path('app/'.$basePath);

        File::ensureDirectoryExists($root.'/classes');
        File::put($root.'/classes/WidgetService.cls', 'public with sharing class WidgetService {}');
        File::put($root.'/classes/WidgetService.cls-meta.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><ApexClass><apiVersion>61.0</apiVersion><status>Active</status></ApexClass>");

        File::ensureDirectoryExists($root.'/triggers');
        File::put($root.'/triggers/WidgetTrigger.trigger', 'trigger WidgetTrigger on Widget__c (before insert) {}');
        File::put($root.'/triggers/WidgetTrigger.trigger-meta.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><ApexTrigger><apiVersion>61.0</apiVersion><status>Active</status></ApexTrigger>");

        File::ensureDirectoryExists($root.'/objects/Widget__c/fields');
        File::ensureDirectoryExists($root.'/objects/Widget__c/validationRules');
        File::put($root.'/objects/Widget__c/Widget__c.object-meta.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><CustomObject><label>Widget</label></CustomObject>");
        File::put($root.'/objects/Widget__c/fields/Score__c.field-meta.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><CustomField><fullName>Score__c</fullName><label>Score</label><type>Number</type><required>false</required></CustomField>");
        File::put($root.'/objects/Widget__c/validationRules/Score_Required.validationRule-meta.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><ValidationRule><fullName>Score_Required</fullName><active>false</active><errorMessage>Score required</errorMessage></ValidationRule>");

        File::ensureDirectoryExists($root.'/flows');
        File::put($root.'/flows/Widget_After_Save.flow-meta.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?><Flow><label>Widget After Save</label><status>Active</status><processType>AutoLaunchedFlow</processType></Flow>");

        MetadataSnapshot::query()->create([
            'salesforce_org_id' => $org->id,
            'sync_run_id' => $sourceRun->id,
            'snapshot_type' => 'source_retrieve',
            'path' => $basePath,
            'metadata' => ['file_count' => 10],
        ]);

        return [$org, $sourceRun];
    }
}