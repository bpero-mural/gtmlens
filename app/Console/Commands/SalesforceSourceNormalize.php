<?php

namespace App\Console\Commands;

use App\Domain\Salesforce\Services\SalesforceSourceNormalizer;
use App\Models\MetadataSnapshot;
use App\Models\SyncRun;
use Illuminate\Console\Command;
use Throwable;

class SalesforceSourceNormalize extends Command
{
    protected $signature = 'salesforce:source-normalize {syncRunId?} {--latest} {--orgAlias=}';

    protected $description = 'Normalize a retrieved Salesforce source snapshot into local metadata tables.';

    public function __construct(private readonly SalesforceSourceNormalizer $normalizer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $snapshot = $this->resolveSourceSnapshot();

        if (! $snapshot instanceof MetadataSnapshot) {
            $this->components->error('No source retrieve snapshot was found. Pass a sync run id or use --latest with an optional --orgAlias.');

            return self::FAILURE;
        }

        $normalizationRun = SyncRun::query()->create([
            'salesforce_org_id' => $snapshot->salesforce_org_id,
            'status' => 'running',
            'triggered_by' => 'source_normalization',
            'started_at' => now(),
        ]);

        try {
            $counts = $this->normalizer->normalize($snapshot, $normalizationRun);

            $normalizationRun->update([
                'status' => 'completed',
                'counts' => $counts,
                'finished_at' => now(),
            ]);

            $total = array_sum($counts);
            $this->components->info("Source normalization completed with {$total} metadata entities.");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $normalizationRun->update([
                'status' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 2000),
                'finished_at' => now(),
            ]);

            $this->components->error($normalizationRun->error_message);

            return self::FAILURE;
        }
    }

    private function resolveSourceSnapshot(): ?MetadataSnapshot
    {
        $syncRunId = $this->argument('syncRunId');

        if ($syncRunId !== null) {
            return MetadataSnapshot::query()
                ->where('sync_run_id', (int) $syncRunId)
                ->where('snapshot_type', 'source_retrieve')
                ->latest()
                ->first();
        }

        if (! (bool) $this->option('latest')) {
            return null;
        }

        $query = MetadataSnapshot::query()
            ->where('snapshot_type', 'source_retrieve')
            ->whereHas('salesforceOrg', function ($query): void {
                $orgAlias = trim((string) $this->option('orgAlias'));

                if ($orgAlias !== '') {
                    $query->where('alias', $orgAlias);
                }
            });

        return $query->latest()->first();
    }
}