<?php

namespace Tests\Feature;

use App\Jobs\RunSalesforceSourceSync;
use App\Livewire\SyncRunsPanel;
use App\Models\SalesforceOrg;
use App\Models\SyncRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class SalesforceSourceSyncUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_runs_panel_requires_authentication(): void
    {
        $this->get('/sync-runs')->assertRedirect('/login');
    }

    public function test_sync_runs_panel_queues_source_sync_job_without_page_post(): void
    {
        Queue::fake();

        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user);

        Livewire::test(SyncRunsPanel::class)
            ->set('orgAlias', 'stage')
            ->call('queueSourceSync')
            ->assertHasNoErrors()
            ->assertSee('Salesforce source sync was queued for [stage].');

        Queue::assertPushed(RunSalesforceSourceSync::class, fn (RunSalesforceSourceSync $job) => $job->orgAlias === 'stage');
    }

    public function test_sync_runs_panel_validates_safe_cli_aliases(): void
    {
        Queue::fake();

        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user);

        Livewire::test(SyncRunsPanel::class)
            ->set('orgAlias', 'stage;Account')
            ->call('queueSourceSync')
            ->assertHasErrors(['orgAlias']);

        Queue::assertNothingPushed();
    }

    public function test_sync_runs_panel_shows_running_progress_and_refresh_action(): void
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
            'status' => 'running',
            'triggered_by' => 'cli_source_retrieve',
            'started_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/sync-runs')
            ->assertOk()
            ->assertSee('Run source sync')
            ->assertSee('Auto-refreshing')
            ->assertSee('Retrieving source')
            ->assertSee('Refresh');
    }

    public function test_sync_runs_panel_shows_expanded_counts(): void
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
            'triggered_by' => 'source_normalization',
            'started_at' => now()->subMinute(),
            'finished_at' => now(),
            'counts' => ['metadata_entities' => 1051, 'custom_fields' => 586],
        ]);

        $this->actingAs($user)
            ->get('/sync-runs')
            ->assertOk()
            ->assertSee('Normalizing metadata')
            ->assertSee('metadata entities: 1051')
            ->assertSee('custom fields: 586');
    }

    public function test_source_sync_job_runs_retrieve_then_normalize(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('salesforce:source-retrieve', ['orgAlias' => 'stage'])
            ->andReturn(0);
        Artisan::shouldReceive('call')
            ->once()
            ->with('salesforce:source-normalize', ['--latest' => true, '--orgAlias' => 'stage'])
            ->andReturn(0);

        (new RunSalesforceSourceSync('stage'))->handle();
    }

    public function test_source_sync_job_stops_when_retrieve_fails(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('salesforce:source-retrieve', ['orgAlias' => 'stage'])
            ->andReturn(1);
        Artisan::shouldReceive('call')
            ->never()
            ->with('salesforce:source-normalize', ['--latest' => true, '--orgAlias' => 'stage']);

        (new RunSalesforceSourceSync('stage'))->handle();
    }
}
