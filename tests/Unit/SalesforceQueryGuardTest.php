<?php

namespace Tests\Unit;

use App\Domain\Salesforce\Guards\SalesforceQueryGuard;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SalesforceQueryGuardTest extends TestCase
{
    public function test_it_allows_allowlisted_metadata_objects(): void
    {
        $guard = new SalesforceQueryGuard();

        foreach (SalesforceQueryGuard::ALLOWED_OBJECTS as $object) {
            $guard->assertAllowed("SELECT Id FROM {$object} LIMIT 1");
        }

        $this->expectNotToPerformAssertions();
    }

    public function test_it_blocks_business_record_objects(): void
    {
        $guard = new SalesforceQueryGuard();

        foreach (['Account', 'Contact', 'Opportunity'] as $object) {
            try {
                $guard->assertAllowed("SELECT Id, Name FROM {$object} LIMIT 1");
                $this->fail("Expected [{$object}] to be blocked.");
            } catch (InvalidArgumentException $exception) {
                $this->assertStringContainsString('not allowed', $exception->getMessage());
            }
        }
    }

    public function test_it_blocks_select_star(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('SELECT * is not allowed');

        (new SalesforceQueryGuard())->assertAllowed('SELECT * FROM EntityDefinition');
    }

    public function test_it_blocks_unknown_objects(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('not allowed');

        (new SalesforceQueryGuard())->assertAllowed('SELECT Id FROM SomethingInternal__c LIMIT 1');
    }
}
