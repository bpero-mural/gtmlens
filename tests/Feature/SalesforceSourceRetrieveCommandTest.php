<?php

namespace Tests\Feature;

use App\Domain\Salesforce\Contracts\SalesforceSourceRetriever;
use App\Domain\Salesforce\Exceptions\SalesforceCliException;
use App\Models\MetadataSnapshot;
use App\Models\SalesforceOrg;
use App\Models\SyncRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SalesforceSourceRetrieveCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(storage_path('app/snapshots/source-test'));
        File::deleteDirectory(storage_path('app/snapshots/stage'));
    }

    public function test_it_fails_before_creating_a_run_when_manifest_is_missing(): void
    {
        $this->artisan('salesforce:source-retrieve stage --manifest=manifest/missing.xml')
            ->assertFailed()
            ->expectsOutputToContain('Source retrieve manifest was not found');

        $this->assertDatabaseCount('sync_runs', 0);
        $this->assertDatabaseCount('metadata_snapshots', 0);
    }

    public function test_it_records_failed_sync_run_when_source_retrieve_fails(): void
    {
        $this->app->bind(SalesforceSourceRetriever::class, fn () => new class implements SalesforceSourceRetriever {
            public function retrieve(string $orgAlias, string $manifestPath, string $outputDir, int $waitMinutes): array
            {
                throw new SalesforceCliException('Authorization: Bearer secret access_token="abc" failed.');
            }
        });

        $this->artisan('salesforce:source-retrieve stage')
            ->assertFailed();

        $syncRun = SyncRun::query()->firstOrFail();

        $this->assertSame('failed', $syncRun->status);
        $this->assertSame('cli_source_retrieve', $syncRun->triggered_by);
        $this->assertStringNotContainsString('Bearer secret', $syncRun->error_message);
        $this->assertStringNotContainsString('abc', $syncRun->error_message);
    }

    public function test_it_records_completed_source_retrieve_run_and_snapshot_directory(): void
    {
        $this->app->bind(SalesforceSourceRetriever::class, fn () => new class implements SalesforceSourceRetriever {
            public function retrieve(string $orgAlias, string $manifestPath, string $outputDir, int $waitMinutes): array
            {
                File::ensureDirectoryExists($outputDir.'/force-app/main/default/classes');
                File::put($outputDir.'/force-app/main/default/classes/Example.cls', 'public class Example {}');
                File::put($outputDir.'/force-app/main/default/classes/Example.cls-meta.xml', '<ApexClass />');

                return [
                    'status' => 0,
                    'result' => [
                        'done' => true,
                    ],
                ];
            }
        });

        $this->artisan('salesforce:source-retrieve source-test')
            ->assertSuccessful();

        $org = SalesforceOrg::query()->where('alias', 'source-test')->firstOrFail();
        $syncRun = SyncRun::query()->firstOrFail();
        $snapshot = MetadataSnapshot::query()->firstOrFail();

        $this->assertSame($org->id, $syncRun->salesforce_org_id);
        $this->assertSame('completed', $syncRun->status);
        $this->assertSame('cli_source_retrieve', $syncRun->triggered_by);
        $this->assertSame(2, $syncRun->counts['source_files']);
        $this->assertSame('source_retrieve', $snapshot->snapshot_type);
        $this->assertSame('snapshots/source-test/'.$syncRun->id.'/source', $snapshot->path);
        $this->assertSame(2, $snapshot->metadata['file_count']);
        $this->assertContains('force-app/main/default/classes/Example.cls', $snapshot->metadata['files']);
        $this->assertFileExists(storage_path('app/'.$snapshot->path.'/force-app/main/default/classes/Example.cls'));
        $this->assertDatabaseCount('metadata_entities', 0);
    }
}
