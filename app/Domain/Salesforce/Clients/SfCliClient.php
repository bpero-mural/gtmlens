<?php

namespace App\Domain\Salesforce\Clients;

use App\Domain\Salesforce\Contracts\SalesforceCliClient;
use App\Domain\Salesforce\DTOs\MetadataProbeQuery;
use App\Domain\Salesforce\Exceptions\SalesforceCliException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class SfCliClient implements SalesforceCliClient
{
    public function query(string $orgAlias, MetadataProbeQuery $query): array
    {
        $command = [
            'sf',
            'data',
            'query',
            '--json',
            '--target-org',
            $orgAlias,
            '--query',
            $query->soql,
        ];

        if ($query->usesToolingApi) {
            $command[] = '--use-tooling-api';
        }

        $process = new Process($command);
        $process->setTimeout(120);

        try {
            $process->run();
        } catch (ProcessStartFailedException $exception) {
            throw new SalesforceCliException('Salesforce CLI executable [sf] is not available in this environment.', previous: $exception);
        } catch (Throwable $exception) {
            throw new SalesforceCliException($this->sanitize($exception->getMessage()), previous: $exception);
        }

        if (! $process->isSuccessful()) {
            $message = trim($process->getErrorOutput()) ?: trim($process->getOutput()) ?: 'Salesforce CLI query failed.';

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
