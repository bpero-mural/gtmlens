<?php

namespace App\Domain\Salesforce\Guards;

use InvalidArgumentException;

class SalesforceQueryGuard
{
    /**
     * @var array<int, string>
     */
    public const ALLOWED_OBJECTS = [
        'EntityDefinition',
        'FieldDefinition',
        'ApexClass',
        'ApexTrigger',
        'ValidationRule',
        'Flow',
        'FlowDefinition',
        'FlowVersionView',
        'CustomObject',
        'CustomField',
        'PicklistValueInfo',
    ];

    public function assertAllowed(string $soql): void
    {
        if (preg_match('/select\s+\*/i', $soql) === 1) {
            throw new InvalidArgumentException('SELECT * is not allowed for Salesforce metadata probes.');
        }

        $object = $this->extractObjectName($soql);

        if (! in_array($object, self::ALLOWED_OBJECTS, true)) {
            throw new InvalidArgumentException("Salesforce object [{$object}] is not allowed for metadata probes.");
        }
    }

    public function extractObjectName(string $soql): string
    {
        if (preg_match('/\bfrom\s+([A-Za-z_][A-Za-z0-9_]*)\b/i', $soql, $matches) !== 1) {
            throw new InvalidArgumentException('Salesforce query must include a FROM object.');
        }

        return $matches[1];
    }
}
