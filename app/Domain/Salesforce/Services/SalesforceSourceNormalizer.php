<?php

namespace App\Domain\Salesforce\Services;

use App\Models\MetadataEntity;
use App\Models\MetadataEntityVersion;
use App\Models\MetadataSnapshot;
use App\Models\SearchDocument;
use App\Models\SyncRun;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class SalesforceSourceNormalizer
{
    /**
     * @return array<string, int>
     */
    public function normalize(MetadataSnapshot $snapshot, SyncRun $normalizationRun): array
    {
        $sourceRoot = storage_path('app/'.$snapshot->path);

        if (! File::isDirectory($sourceRoot)) {
            throw new \RuntimeException("Source snapshot directory was not found: {$snapshot->path}");
        }

        $counts = [];

        foreach ($this->discoverEntities($sourceRoot) as $payload) {
            $entity = $this->upsertEntity($snapshot, $normalizationRun, $payload);
            $this->upsertSearchDocument($entity, $payload);
            $counts[$payload['metadata_type']] = ($counts[$payload['metadata_type']] ?? 0) + 1;
        }

        ksort($counts);

        return $counts;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function discoverEntities(string $sourceRoot): array
    {
        $files = collect(File::allFiles($sourceRoot));
        $byRelativePath = $files->keyBy(fn (SplFileInfo $file): string => $this->relativePath($sourceRoot, $file->getPathname()));
        $entities = [];

        foreach ($files as $file) {
            $relativePath = $this->relativePath($sourceRoot, $file->getPathname());

            if (str_ends_with($relativePath, '.cls')) {
                $apiName = basename($relativePath, '.cls');
                $metaPath = $relativePath.'-meta.xml';
                $entities[] = $this->codeEntity($sourceRoot, 'ApexClass', $apiName, $relativePath, $byRelativePath->get($metaPath));
            } elseif (str_ends_with($relativePath, '.trigger')) {
                $apiName = basename($relativePath, '.trigger');
                $metaPath = $relativePath.'-meta.xml';
                $entities[] = $this->codeEntity($sourceRoot, 'ApexTrigger', $apiName, $relativePath, $byRelativePath->get($metaPath));
            } elseif (preg_match('#(^|/)objects/([^/]+)/([^/]+)\.object-meta\.xml$#', $relativePath, $matches) === 1) {
                $objectApiName = $matches[2];
                $entities[] = $this->xmlEntity($sourceRoot, 'CustomObject', $objectApiName, $objectApiName, $relativePath, null);
            } elseif (preg_match('#(^|/)objects/([^/]+)/fields/([^/]+)\.field-meta\.xml$#', $relativePath, $matches) === 1) {
                $objectApiName = $matches[2];
                $fieldApiName = $matches[3];
                $entities[] = $this->xmlEntity($sourceRoot, 'CustomField', $objectApiName.'.'.$fieldApiName, $fieldApiName, $relativePath, 'CustomObject:'.$objectApiName);
            } elseif (preg_match('#(^|/)objects/([^/]+)/validationRules/([^/]+)\.validationRule-meta\.xml$#', $relativePath, $matches) === 1) {
                $objectApiName = $matches[2];
                $ruleApiName = $matches[3];
                $entities[] = $this->xmlEntity($sourceRoot, 'ValidationRule', $objectApiName.'.'.$ruleApiName, $ruleApiName, $relativePath, 'CustomObject:'.$objectApiName);
            } elseif (preg_match('#(^|/)flows/([^/]+)\.flow-meta\.xml$#', $relativePath, $matches) === 1) {
                $flowApiName = $matches[2];
                $entities[] = $this->xmlEntity($sourceRoot, 'Flow', $flowApiName, $flowApiName, $relativePath, null);
            }
        }

        return $entities;
    }

    /**
     * @return array<string, mixed>
     */
    private function codeEntity(string $sourceRoot, string $type, string $apiName, string $sourcePath, ?SplFileInfo $metaFile): array
    {
        $sourceFullPath = storage_path('app/__placeholder__');
        $meta = [];
        $contentPieces = [File::get($sourceRoot.'/'.$sourcePath)];

        if ($metaFile instanceof SplFileInfo) {
            $meta = $this->readXmlMetadata($metaFile->getPathname());
            $contentPieces[] = File::get($metaFile->getPathname());
        }

        $hash = hash('sha256', implode("\n", $contentPieces));

        return [
            'external_key' => $type.':'.$apiName,
            'metadata_type' => $type,
            'api_name' => $apiName,
            'label' => $apiName,
            'parent_external_key' => null,
            'status' => $meta['status'] ?? 'active',
            'content_hash' => $hash,
            'primary_path' => $sourcePath,
            'attributes' => array_filter([
                'source_path' => $sourcePath,
                'meta_path' => $metaFile instanceof SplFileInfo ? $this->relativePath($sourceRoot, $metaFile->getPathname()) : null,
                'api_version' => $meta['apiVersion'] ?? null,
                'status' => $meta['status'] ?? null,
            ], fn ($value) => $value !== null),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function xmlEntity(string $sourceRoot, string $type, string $keyName, string $apiName, string $relativePath, ?string $parentExternalKey): array
    {
        $absolutePath = $sourceRoot.'/'.$relativePath;
        $meta = $this->readXmlMetadata($absolutePath);
        $label = $meta['label'] ?? $meta['fullName'] ?? $apiName;

        return [
            'external_key' => $type.':'.$keyName,
            'metadata_type' => $type,
            'api_name' => $keyName,
            'label' => $label,
            'parent_external_key' => $parentExternalKey,
            'status' => $this->statusFromMetadata($meta),
            'content_hash' => hash_file('sha256', $absolutePath),
            'primary_path' => $relativePath,
            'attributes' => array_filter([
                'source_path' => $relativePath,
                'full_name' => $meta['fullName'] ?? null,
                'label' => $meta['label'] ?? null,
                'type' => $meta['type'] ?? null,
                'required' => $meta['required'] ?? null,
                'active' => $meta['active'] ?? null,
                'status' => $meta['status'] ?? null,
                'process_type' => $meta['processType'] ?? null,
            ], fn ($value) => $value !== null),
        ];
    }

    private function upsertEntity(MetadataSnapshot $snapshot, SyncRun $normalizationRun, array $payload): MetadataEntity
    {
        $now = now();

        $entity = MetadataEntity::query()->firstOrNew([
            'salesforce_org_id' => $snapshot->salesforce_org_id,
            'external_key' => $payload['external_key'],
        ]);

        if (! $entity->exists) {
            $entity->first_seen_at = $now;
        }

        $entity->fill([
            'metadata_type' => $payload['metadata_type'],
            'api_name' => $payload['api_name'],
            'label' => $payload['label'],
            'parent_external_key' => $payload['parent_external_key'],
            'status' => $payload['status'],
            'content_hash' => $payload['content_hash'],
            'attributes' => $payload['attributes'],
            'last_seen_at' => $now,
        ]);
        $entity->save();

        $hasVersion = MetadataEntityVersion::query()
            ->where('metadata_entity_id', $entity->id)
            ->where('content_hash', $payload['content_hash'])
            ->exists();

        if (! $hasVersion) {
            $nextVersion = ((int) MetadataEntityVersion::query()
                ->where('metadata_entity_id', $entity->id)
                ->max('version_number')) + 1;

            MetadataEntityVersion::query()->create([
                'metadata_entity_id' => $entity->id,
                'sync_run_id' => $normalizationRun->id,
                'version_number' => $nextVersion,
                'content_hash' => $payload['content_hash'],
                'payload_path' => $snapshot->path.'/'.$payload['primary_path'],
                'normalized_payload' => $payload,
            ]);
        }

        return $entity;
    }

    private function upsertSearchDocument(MetadataEntity $entity, array $payload): void
    {
        SearchDocument::query()->updateOrCreate([
            'metadata_entity_id' => $entity->id,
        ], [
            'title' => $payload['label'] ?: $payload['api_name'],
            'api_name' => $payload['api_name'],
            'metadata_type' => $payload['metadata_type'],
            'content' => trim(implode(' ', array_filter([
                $payload['metadata_type'],
                $payload['api_name'],
                $payload['label'],
                $payload['parent_external_key'],
                data_get($payload, 'attributes.type'),
                data_get($payload, 'attributes.process_type'),
            ]))),
            'filters' => [
                'metadata_type' => $payload['metadata_type'],
                'status' => $payload['status'],
                'parent_external_key' => $payload['parent_external_key'],
            ],
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function readXmlMetadata(string $path): array
    {
        $xml = @simplexml_load_file($path);

        if ($xml === false) {
            return [];
        }

        $values = [];

        foreach (['fullName', 'label', 'type', 'required', 'active', 'status', 'processType', 'apiVersion'] as $key) {
            if (isset($xml->{$key})) {
                $values[$key] = trim((string) $xml->{$key});
            }
        }

        return $values;
    }

    /**
     * @param array<string, string> $meta
     */
    private function statusFromMetadata(array $meta): string
    {
        if (array_key_exists('active', $meta)) {
            return filter_var($meta['active'], FILTER_VALIDATE_BOOLEAN) ? 'active' : 'inactive';
        }

        return $meta['status'] ?? 'active';
    }

    private function relativePath(string $root, string $path): string
    {
        return str_replace('\\', '/', ltrim(str_replace($root, '', $path), DIRECTORY_SEPARATOR));
    }

    private function absoluteFromRelative(string $relativePath): string
    {
        return storage_path('app/'.trim($relativePath, '/'));
    }
}