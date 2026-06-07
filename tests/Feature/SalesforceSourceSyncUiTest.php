<?php

namespace Tests\Feature;

use App\Jobs\RunSalesforceSourceSync;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SalesforceSourceSyncUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_sync_trigger_requires_authentication(): void
    {
        $this->withSession(['_token' => 'test-token'])->post('/sync-runs/source-sync', ['org_alias' => 'stage', '_token' => 'test-token'])
            ->assertRedirect('/login');
    }

    public function test_sync_trigger_queues_source_sync_job(): void
    {
        Queue::fake();

        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])->post('/sync-runs/source-sync', ['org_alias' => 'stage', '_token' => 'test-token'])
            ->assertRedirect('/sync-runs')
            ->assertSessionHas('status');

        Queue::assertPushed(RunSalesforceSourceSync::class, fn (RunSalesforceSourceSync $job) => $job->orgAlias === 'stage');
    }

    public function test_sync_trigger_validates_safe_cli_aliases(): void
    {
        Queue::fake();

        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->from('/sync-runs')
            ->withSession(['_token' => 'test-token'])->post('/sync-runs/source-sync', ['org_alias' => 'stage;Account', '_token' => 'test-token'])
            ->assertRedirect('/sync-runs')
            ->assertSessionHasErrors('org_alias');

        Queue::assertNothingPushed();
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
