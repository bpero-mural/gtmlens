<?php

namespace App\Console\Commands;

use App\Domain\Salesforce\Contracts\SalesforceSourceRetriever;
use App\Domain\Salesforce\Exceptions\SalesforceCliException;
use App\Models\MetadataSnapshot;
use App\Models\SalesforceOrg;
use App\Models\SyncRun;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class SalesforceSourceRetrieve extends Command
{
    protected $signature = 'salesforce:source-retrieve {orgAlias} {--manifest=manifest/gtm-lens-source.xml} {--wait=10}';

    protected $description = 'Retrieve allowlisted Salesforce metadata source through Salesforce CLI without storing Salesforce tokens.';

    public function __construct(private readonly SalesforceSourceRetriever $retriever)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $orgAlias = trim((string) $this->argument('orgAlias'));
        $manifest = trim((string) $this->option('manifest'));
        $wait = max(1, (int) $this->option('wait'));

        if ($orgAlias === '') {
            $this->components->error('A Salesforce CLI org alias is required.');

            return self::FAILURE;
        }

        $manifestPath = base_path($manifest);

        if (! File::exists($manifestPath)) {
            $this->components->error("Source retrieve manifest was not found: {$manifest}");

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
            'triggered_by' => 'cli_source_retrieve',
            'started_at' => now(),
        ]);

        $snapshotBase = 'snapshots/'.$this->safePathSegment($orgAlias).'/'.$syncRun->id.'/source';
        $outputDir = storage_path('app/'.$snapshotBase);

        try {
            File::ensureDirectoryExists($outputDir);

            $payload = $this->retriever->retrieve($orgAlias, $manifestPath, $outputDir, $wait);
            $files = collect(File::allFiles($outputDir))
                ->map(fn ($file) => str_replace('\\', '/', $file->getRelativePathname()))
                ->sort()
                ->values();

            $encoded = json_encode([
                'manifest' => $manifest,
                'cli_result' => $payload,
                'files' => $files,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if ($encoded === false) {
                throw new SalesforceCliException('Could not encode source retrieve snapshot metadata.');
            }

            MetadataSnapshot::query()->create([
                'salesforce_org_id' => $org->id,
                'sync_run_id' => $syncRun->id,
                'snapshot_type' => 'source_retrieve',
                'path' => $snapshotBase,
                'content_hash' => hash('sha256', $encoded),
                'metadata' => [
                    'manifest' => $manifest,
                    'file_count' => $files->count(),
                    'files' => $files,
                ],
            ]);

            $syncRun->update([
                'status' => 'completed',
                'counts' => ['source_files' => $files->count()],
                'finished_at' => now(),
            ]);

            $this->components->info("Source retrieve completed for [{$orgAlias}] with {$files->count()} files.");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $syncRun->update([
                'status' => 'failed',
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
