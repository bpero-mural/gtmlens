<?php

namespace Tests\Feature;

use App\Models\SalesforceOrg;
use App\Models\SyncRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_authenticated_users(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Livewire active')
            ->assertSee('Stage 0');
    }

    public function test_placeholder_pages_are_protected_and_render(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        foreach (['/salesforce-orgs', '/search', '/dictionary', '/timeline', '/issues', '/sync-runs', '/admin'] as $path) {
            $this->get($path)->assertRedirect('/login');
        }

        foreach (['/salesforce-orgs', '/search', '/dictionary', '/timeline', '/issues', '/sync-runs', '/admin'] as $path) {
            $this->actingAs($user)->get($path)->assertOk();
        }
    }

    public function test_salesforce_orgs_page_shows_cli_placeholder_orgs(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        SalesforceOrg::query()->create([
            'name' => 'stage',
            'alias' => 'stage',
            'api_version' => '61.0',
            'status' => 'cli_placeholder',
        ]);

        $this->actingAs($user)
            ->get('/salesforce-orgs')
            ->assertOk()
            ->assertSee('stage')
            ->assertSee('cli_placeholder');
    }

    public function test_sync_runs_page_shows_probe_runs(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $org = SalesforceOrg::query()->create([
            'name' => 'stage',
            'alias' => 'stage',
            'api_version' => '61.0',
            'status' => 'cli_placeholder',
        ]);

        SyncRun::query()->create([
            'salesforce_org_id' => $org->id,
            'status' => 'completed',
            'triggered_by' => 'cli_probe',
            'started_at' => now(),
            'finished_at' => now(),
            'counts' => ['entity_definitions' => 2],
        ]);

        $this->actingAs($user)
            ->get('/sync-runs')
            ->assertOk()
            ->assertSee('stage')
            ->assertSee('cli_probe')
            ->assertSee('completed');
    }
}
