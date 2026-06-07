<?php

namespace Tests\Feature;

use App\Domain\Salesforce\Contracts\SalesforceCliClient;
use App\Domain\Salesforce\DTOs\MetadataProbeQuery;
use App\Domain\Salesforce\Exceptions\SalesforceCliException;
use App\Domain\Salesforce\Services\MetadataProbeQueryCatalog;
use App\Models\MetadataSnapshot;
use App\Models\SalesforceOrg;
use App\Models\SyncRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SalesforceMetadataProbeCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_clearly_when_salesforce_cli_is_unavailable(): void
    {
        $this->app->bind(SalesforceCliClient::class, fn () => new class implements SalesforceCliClient {
            public function query(string $orgAlias, MetadataProbeQuery $query): array
            {
                throw new SalesforceCliException('Salesforce CLI executable [sf] is not available in this environment.');
            }
        });

        $this->artisan('salesforce:metadata-probe stage')
            ->assertFailed()
            ->expectsOutputToContain('Salesforce CLI executable [sf] is not available');

        $this->assertDatabaseHas('sync_runs', [
            'status' => 'failed',
            'triggered_by' => 'cli_probe',
        ]);
    }

    public function test_it_records_failed_sync_run_when_cli_returns_an_error(): void
    {
        $this->app->bind(SalesforceCliClient::class, fn () => new class implements SalesforceCliClient {
            public function query(string $orgAlias, MetadataProbeQuery $query): array
            {
                throw new SalesforceCliException('Authorization: Bearer secret access_token="abc" failed.');
            }
        });

        $this->artisan('salesforce:metadata-probe stage')
            ->assertFailed();

        $syncRun = SyncRun::query()->firstOrFail();

        $this->assertSame('failed', $syncRun->status);
        $this->assertStringNotContainsString('Bearer secret', $syncRun->error_message);
        $this->assertStringNotContainsString('abc', $syncRun->error_message);
    }

    public function test_it_records_completed_sync_run_counts_and_snapshots(): void
    {
        Storage::fake('local');

        $this->app->bind(SalesforceCliClient::class, fn () => new class implements SalesforceCliClient {
            public function query(string $orgAlias, MetadataProbeQuery $query): array
            {
                return [
                    'status' => 0,
                    'result' => [
                        'records' => [
                            ['Id' => $query->key.'-1'],
                            ['Id' => $query->key.'-2'],
                        ],
                    ],
                ];
            }
        });

        $this->artisan('salesforce:metadata-probe stage')
            ->assertSuccessful();

        $org = SalesforceOrg::query()->where('alias', 'stage')->firstOrFail();
        $syncRun = SyncRun::query()->firstOrFail();
        $expectedSnapshotCount = count((new MetadataProbeQueryCatalog())->all());

        $this->assertSame($org->id, $syncRun->salesforce_org_id);
        $this->assertSame('completed', $syncRun->status);
        $this->assertSame('cli_probe', $syncRun->triggered_by);
        $this->assertSame($expectedSnapshotCount, MetadataSnapshot::query()->count());
        $this->assertDatabaseCount('metadata_entities', 0);

        foreach (MetadataSnapshot::query()->get() as $snapshot) {
            Storage::disk('local')->assertExists($snapshot->path);
            $this->assertSame(2, $snapshot->metadata['record_count']);
        }

        foreach ($syncRun->counts as $count) {
            $this->assertSame(2, $count);
        }
    }
}
