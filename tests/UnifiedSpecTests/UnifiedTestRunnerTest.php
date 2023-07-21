<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Tests\FunctionalTestCase;

class UnifiedTestRunnerTest extends FunctionalTestCase
{
    public function testEntityMapObserver(): void
    {
        $test = UnifiedTestCase::fromFile(__DIR__ . '/runner/entity-map-observer.json');
        $calls = 0;

        $runner = new UnifiedTestRunner(static::getUri());
        $runner->setEntityMapObserver(function (EntityMap $entityMap) use (&$calls): void {
            $this->assertArrayHasKey('client0', $entityMap);
            $this->assertArrayHasKey('database0', $entityMap);
            $this->assertArrayHasKey('collection0', $entityMap);
            $calls++;
        });

        $runner->run($test->current());
        $this->assertSame(1, $calls);
    }
}
