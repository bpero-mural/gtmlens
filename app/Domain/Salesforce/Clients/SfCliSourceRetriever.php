<?php

namespace App\Domain\Salesforce\Clients;

use App\Domain\Salesforce\Contracts\SalesforceSourceRetriever;
use App\Domain\Salesforce\Exceptions\SalesforceCliException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class SfCliSourceRetriever implements SalesforceSourceRetriever
{
    /**
     * @return array<string, mixed>
     */
    public function retrieve(string $orgAlias, string $manifestPath, string $outputDir, int $waitMinutes): array
    {
        $command = [
            'sf',
            'project',
            'retrieve',
            'start',
            '--json',
            '--target-org',
            $orgAlias,
            '--manifest',
            $manifestPath,
            '--output-dir',
            $outputDir,
            '--wait',
            (string) $waitMinutes,
            '--ignore-conflicts',
        ];

        $apiVersion = config('services.salesforce.api_version', env('SALESFORCE_API_VERSION'));

        if (is_string($apiVersion) && $apiVersion !== '') {
            $command[] = '--api-version';
            $command[] = $apiVersion;
        }

        $process = new Process($command, base_path());
        $process->setTimeout(max(120, ($waitMinutes + 1) * 60));

        try {
            $process->run();
        } catch (ProcessStartFailedException $exception) {
            throw new SalesforceCliException('Salesforce CLI executable [sf] is not available in this environment.', previous: $exception);
        } catch (Throwable $exception) {
            throw new SalesforceCliException($this->sanitize($exception->getMessage()), previous: $exception);
        }

        if (! $process->isSuccessful()) {
            $message = trim($process->getErrorOutput()) ?: trim($process->getOutput()) ?: 'Salesforce CLI source retrieve failed.';

            throw new SalesforceCliException($this->sanitize($message));
        }

        $payload = json_decode($process->getOutput(), true);

        if (! is_array($payload)) {
            throw new SalesforceCliException('Salesforce CLI returned invalid JSON.');
        }

        return $payload;
    }

    private function sanitize(string $message): string
    {
        $message = preg_replace('/Bearer\s+[A-Za-z0-9._~+\/=-]+/i', 'Bearer [redacted]', $message) ?? $message;
        $message = preg_replace('/("?(?:access|refresh)_token"?\s*[:=]\s*)"[^"]+"/i', '$1"[redacted]"', $message) ?? $message;

        return mb_substr($message, 0, 2000);
    }
}
