<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $currentDate update
 */
class CurrentDateOperatorTest extends UpdateTestCase
{
    public function testSetCurrentDateAndTimestamp(): void
    {
        $update = new Update(
            Update::currentDate(...['lastModified' => true, 'cancellation.date' => ['$type' => 'timestamp']]),
            Update::set(...['cancellation.reason' => 'user request'], status: 'D'),
        );
        /*
        $update = new Update(
            Update::currentDate('lastModified'),
            Update::currentDate('cancellation.date', type: 'timestamp'),
            Update::set('cancellation.reason', 'user request'),
            Update::set('status', 'D'),
        );
        */
        $this->assertSameUpdate(Pipelines::CurrentDateSetCurrentDateAndTimestamp, $update);
    }
}
