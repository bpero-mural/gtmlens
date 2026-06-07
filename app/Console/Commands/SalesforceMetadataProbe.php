<?php

namespace App\Console\Commands;

use App\Domain\Salesforce\Contracts\SalesforceCliClient;
use App\Domain\Salesforce\Exceptions\SalesforceCliException;
use App\Domain\Salesforce\Guards\SalesforceQueryGuard;
use App\Domain\Salesforce\Services\MetadataProbeQueryCatalog;
use App\Models\MetadataSnapshot;
use App\Models\SalesforceOrg;
use App\Models\SyncRun;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SalesforceMetadataProbe extends Command
{
    protected $signature = 'salesforce:metadata-probe {orgAlias}';

    protected $description = 'Run a local Salesforce CLI metadata probe without storing Salesforce tokens.';

    public function __construct(
        private readonly SalesforceCliClient $cli,
        private readonly MetadataProbeQueryCatalog $catalog,
        private readonly SalesforceQueryGuard $guard,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $orgAlias = trim((string) $this->argument('orgAlias'));

        if ($orgAlias === '') {
            $this->components->error('A Salesforce CLI org alias is required.');

            return self::FAILURE;
        }

        $org = SalesforceOrg::query()->updateOrCreate([
            'alias' => $orgAlias,
        ], [
            'name' => $orgAlias,
            'api_version' => config('services.salesforce.api_version', env('SALESFORCE_API_VERSION', '61.0')),
            'status' => 'cli_placeholder',
        ]);

        $syncRun = SyncRun::query()->create([
            'salesforce_org_id' => $org->id,
            'status' => 'running',
            'triggered_by' => 'cli_probe',
            'started_at' => now(),
        ]);

        $counts = [];
        $snapshotBase = 'snapshots/'.$this->safePathSegment($orgAlias).'/'.$syncRun->id;

        try {
            foreach ($this->catalog->all() as $query) {
                $this->guard->assertAllowed($query->soql);

                $payload = $this->cli->query($orgAlias, $query);
                $records = data_get($payload, 'result.records', []);
                $recordCount = is_array($records) ? count($records) : 0;
                $counts[$query->key] = $recordCount;

                $path = "{$snapshotBase}/{$query->key}.json";
                $encoded = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                if ($encoded === false) {
                    throw new SalesforceCliException("Could not encode snapshot payload for [{$query->key}].");
                }

                Storage::disk('local')->put($path, $encoded);

                MetadataSnapshot::query()->create([
                    'salesforce_org_id' => $org->id,
                    'sync_run_id' => $syncRun->id,
                    'snapshot_type' => $query->key,
                    'path' => $path,
                    'content_hash' => hash('sha256', $encoded),
                    'metadata' => [
                        'record_count' => $recordCount,
                        'tooling_api' => $query->usesToolingApi,
                    ],
                ]);
            }

            $syncRun->update([
                'status' => 'completed',
                'counts' => $counts,
                'finished_at' => now(),
            ]);

            $this->components->info("Metadata probe completed for [{$orgAlias}].");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $syncRun->update([
                'status' => 'failed',
                'counts' => $counts ?: null,
                'error_message' => $this->sanitizeError($exception->getMessage()),
                'finished_at' => now(),
            ]);

            $this->components->error($syncRun->error_message);

            return self::FAILURE;
        }
    }

    private function safePathSegment(string $value): string
    {
        $safe = preg_replace('/[^A-Za-z0-9_.-]+/', '-', $value) ?? 'org';

        return trim($safe, '-') ?: 'org';
    }

    private function sanitizeError(string $message): string
    {
        $message = preg_replace('/Bearer\s+[A-Za-z0-9._~+\/=-]+/i', 'Bearer [redacted]', $message) ?? $message;
        $message = preg_replace('/("?(?:access|refresh)_token"?\s*[:=]\s*)"[^"]+"/i', '$1"[redacted]"', $message) ?? $message;

        return mb_substr($message, 0, 2000);
    }
}
