<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\SDAMSubscriber;
use MongoDB\Driver\Monitoring\Subscriber;
use MongoDB\Monitoring\CommandEvents;
use MongoDB\Monitoring\SDAMEvents;

use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;

class MonitoringTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     *
     * @dataProvider provideSubscribers
     */
    public function testSubscriber(Subscriber $subscriber): void
    {
        // Fatal error if the trait does not implement all methods required by the interface
        addSubscriber($subscriber);
        removeSubscriber($subscriber);
    }

    public static function provideSubscribers(): iterable
    {
        yield 'Command' => [
            new class implements CommandSubscriber {
                use CommandEvents;
            },
        ];

        yield 'SDAM' => [
            new class implements SDAMSubscriber {
                use SDAMEvents;
            },
        ];
    }
}
