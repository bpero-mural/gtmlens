<?php

namespace Tests\Feature;

use App\Models\MetadataEntity;
use App\Models\MetadataEntityVersion;
use App\Models\SalesforceOrg;
use App\Models\SearchDocument;
use App\Models\SyncRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetadataBrowserTest extends TestCase
{
    use RefreshDatabase;

    public function test_metadata_browser_is_protected_and_shows_empty_state(): void
    {
        $this->get('/search')->assertRedirect('/login');

        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/search')
            ->assertOk()
            ->assertSee('No metadata found')
            ->assertSee('Run source retrieve and source normalization');
    }

    public function test_metadata_browser_lists_and_filters_normalized_entities(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $org = SalesforceOrg::query()->create([
            'name' => 'stage',
            'alias' => 'stage',
            'status' => 'cli_placeholder',
        ]);

        $class = $this->entity($org->id, 'ApexClass:WidgetService', 'ApexClass', 'WidgetService', 'WidgetService');
        $field = $this->entity($org->id, 'CustomField:Widget__c.Score__c', 'CustomField', 'Widget__c.Score__c', 'Score', 'CustomObject:Widget__c');
        $this->entity($org->id, 'Flow:Invoice_After_Save', 'Flow', 'Invoice_After_Save', 'Invoice After Save');

        SearchDocument::query()->create([
            'metadata_entity_id' => $class->id,
            'title' => 'WidgetService',
            'api_name' => 'WidgetService',
            'metadata_type' => 'ApexClass',
            'content' => 'ApexClass WidgetService widget calculation service',
        ]);
        SearchDocument::query()->create([
            'metadata_entity_id' => $field->id,
            'title' => 'Score',
            'api_name' => 'Widget__c.Score__c',
            'metadata_type' => 'CustomField',
            'content' => 'CustomField Widget__c.Score__c Score Number',
        ]);

        $this->actingAs($user)
            ->get('/search?q=score&type=CustomField')
            ->assertOk()
            ->assertSee('Widget__c.Score__c')
            ->assertSee('CustomField')
            ->assertDontSee('WidgetService')
            ->assertDontSee('Invoice_After_Save');
    }

    public function test_metadata_detail_page_shows_entity_attributes_and_latest_version(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $org = SalesforceOrg::query()->create([
            'name' => 'stage',
            'alias' => 'stage',
            'status' => 'cli_placeholder',
        ]);
        $syncRun = SyncRun::query()->create([
            'salesforce_org_id' => $org->id,
            'status' => 'completed',
            'triggered_by' => 'source_normalization',
        ]);
        $entity = $this->entity($org->id, 'CustomField:Widget__c.Score__c', 'CustomField', 'Widget__c.Score__c', 'Score', 'CustomObject:Widget__c', [
            'source_path' => 'objects/Widget__c/fields/Score__c.field-meta.xml',
            'type' => 'Number',
            'required' => 'false',
        ]);

        MetadataEntityVersion::query()->create([
            'metadata_entity_id' => $entity->id,
            'sync_run_id' => $syncRun->id,
            'version_number' => 1,
            'content_hash' => 'abc123',
            'payload_path' => 'snapshots/stage/8/source/objects/Widget__c/fields/Score__c.field-meta.xml',
            'normalized_payload' => ['api_name' => 'Widget__c.Score__c'],
        ]);
        SearchDocument::query()->create([
            'metadata_entity_id' => $entity->id,
            'title' => 'Score',
            'api_name' => 'Widget__c.Score__c',
            'metadata_type' => 'CustomField',
            'content' => 'CustomField Widget__c.Score__c Score Number',
        ]);

        $this->actingAs($user)
            ->get('/metadata/'.$entity->id)
            ->assertOk()
            ->assertSee('Widget__c.Score__c')
            ->assertSee('CustomField')
            ->assertSee('CustomObject:Widget__c')
            ->assertSee('Number')
            ->assertSee('snapshots/stage/8/source/objects/Widget__c/fields/Score__c.field-meta.xml')
            ->assertSee('CustomField Widget__c.Score__c Score Number');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function entity(int $orgId, string $externalKey, string $type, string $apiName, string $label, ?string $parent = null, array $attributes = []): MetadataEntity
    {
        return MetadataEntity::query()->create([
            'salesforce_org_id' => $orgId,
            'external_key' => $externalKey,
            'metadata_type' => $type,
            'api_name' => $apiName,
            'label' => $label,
            'parent_external_key' => $parent,
            'status' => 'active',
            'content_hash' => hash('sha256', $externalKey),
            'attributes' => $attributes,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
    }
}
