<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Tests\TestCase;

class StageZeroFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_stage_zero_tables_are_created(): void
    {
        $tables = [
            'salesforce_orgs',
            'sync_runs',
            'metadata_snapshots',
            'metadata_entities',
            'metadata_entity_versions',
            'metadata_edges',
            'search_documents',
            'dictionary_entries',
            'tags',
            'taggings',
            'change_events',
            'potential_issues',
            'app_audit_logs',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table [{$table}].");
        }
    }

    public function test_core_stage_zero_columns_are_present(): void
    {
        $this->assertTrue(Schema::hasColumns('users', ['name', 'email', 'password', 'role']));
        $this->assertTrue(Schema::hasColumns('salesforce_orgs', ['name', 'alias', 'api_version', 'status']));
        $this->assertTrue(Schema::hasColumns('metadata_entities', ['external_key', 'metadata_type', 'api_name', 'content_hash', 'attributes']));
        $this->assertTrue(Schema::hasColumns('search_documents', ['metadata_entity_id', 'title', 'api_name', 'content', 'filters']));
        $this->assertTrue(Schema::hasColumns('dictionary_entries', ['business_definition', 'owner_name', 'data_classification', 'criticality']));
        $this->assertTrue(Schema::hasColumns('potential_issues', ['issue_type', 'severity', 'status', 'title', 'details']));
        $this->assertTrue(Schema::hasColumns('app_audit_logs', ['user_id', 'action', 'auditable_type', 'auditable_id', 'metadata']));
    }

    public function test_livewire_is_installed_for_the_dashboard_component(): void
    {
        $this->assertTrue(class_exists(Component::class));

        $this->actingAs(\App\Models\User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertSee('Livewire active');
    }

    public function test_node_and_environment_contracts_are_documented(): void
    {
        $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
        $envExample = file_get_contents(base_path('.env.example'));

        $this->assertSame('22', trim(file_get_contents(base_path('.nvmrc'))));
        $this->assertSame('>=22 <23', $package['engines']['node']);
        $this->assertStringContainsString('APP_NAME="GTM Lens"', $envExample);
        $this->assertStringContainsString('DB_CONNECTION=pgsql', $envExample);
        $this->assertStringContainsString('QUEUE_CONNECTION=database', $envExample);
        $this->assertStringContainsString('SECURITY_ALLOW_APEX_STORAGE=false', $envExample);
        $this->assertStringContainsString('SECURITY_ALLOW_RAW_METADATA_VIEW=false', $envExample);
        $this->assertStringContainsString('LOCAL_ADMIN_ENSURE_ON_BOOT=true', $envExample);
    }

    public function test_stage_zero_docs_capture_security_license_and_stage_boundaries(): void
    {
        $security = file_get_contents(base_path('docs/security.md'));
        $licenses = file_get_contents(base_path('docs/license-policy.md'));
        $sync = file_get_contents(base_path('docs/salesforce-sync.md'));
        $design = file_get_contents(base_path('DESIGN.md'));
        $agents = file_get_contents(base_path('AGENTS.md'));

        $this->assertStringContainsString('must not read Salesforce business record data', $security);
        $this->assertStringContainsString('AGPL', $licenses);
        $this->assertStringContainsString('OpenSearch', $licenses);
        $this->assertStringContainsString('OAuth and token storage are intentionally not implemented yet', $sync);
        $this->assertStringContainsString('The MVP must not query or store Salesforce business record data', $sync);
        $this->assertStringContainsString('GTM Lens Design System', $design);
        $this->assertStringContainsString('no Salesforce business record data', $design);
        $this->assertStringContainsString('Google DESIGN.md Legal And Vendor Boundary', $agents);
        $this->assertStringContainsString('not legal advice', $agents);
        $this->assertStringContainsString('Not allowed in the MVP: uploading screenshots', $agents);
        $this->assertStringContainsString('local email/password auth', $agents);
        $this->assertStringContainsString('first file agents should read', $agents);
        $this->assertStringContainsString('admin@example.test', $agents);
        $this->assertStringContainsString('Stage 1A Salesforce CLI metadata probe', $agents);
        $this->assertFileDoesNotExist(base_path('CLAUDE.md'));
    }

    public function test_required_blade_ui_components_exist(): void
    {
        $components = [
            'button',
            'input',
            'textarea',
            'select',
            'checkbox',
            'badge',
            'card',
            'table',
            'tabs',
            'modal',
            'drawer',
            'dropdown',
            'alert',
            'empty-state',
            'skeleton',
            'breadcrumb',
            'pagination',
        ];

        foreach ($components as $component) {
            $this->assertFileExists(resource_path("views/components/ui/{$component}.blade.php"));
        }
    }

    public function test_forbidden_stage_zero_frontend_dependencies_are_not_declared(): void
    {
        $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
        $declared = array_merge($package['dependencies'] ?? [], $package['devDependencies'] ?? []);

        foreach (['vue', '@inertiajs/vue3', 'primevue', 'daisyui', 'ag-grid-community', 'cytoscape'] as $packageName) {
            $this->assertArrayNotHasKey($packageName, $declared);
        }
    }
}
