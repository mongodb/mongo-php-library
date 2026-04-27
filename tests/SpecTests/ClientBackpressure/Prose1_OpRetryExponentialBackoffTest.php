<?php

namespace MongoDB\Tests\SpecTests\ClientBackpressure;

use MongoDB\Driver\Exception\ServerException;
use MongoDB\Driver\Session;
use MongoDB\Operation\WithTransaction;
use MongoDB\Tests\SpecTests\FunctionalTestCase;
use MongoDB\Tests\UnifiedSpecTests\Util;

use function abs;
use function hrtime;

/**
 * Prose test 1: Retry operation uses exponential backoff
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/client-backpressure/tests/README.md#test-1-operation-retry-uses-exponential-backoff
 */
class Prose1_OpRetryExponentialBackoffTest extends FunctionalTestCase
{
    public function testOperationRetryUsesExponentialBackoff(): void
    {
        $this->skipIfTransactionsAreNotSupported();
        $this->skipIfServerVersion('<', '4.3.1', 'Test requires configureFailPoint to support errorLabels');

        $client = self::createTestClient();
        $collection = $client->getCollection($this->getDatabaseName(), $this->getCollectionName());

        $callback = static function (Session $session) use ($collection): void {
            $collection->insertOne(['a' => 1], ['session' => $session]);
        };

        $operation = new WithTransaction($callback);
        $session = $client->startSession();

        Util::setFixedJitter($operation, 0);
        $noBackoffTime = $this->getOperationExecutionTime($session, $operation);

        Util::setFixedJitter($operation, 1);
        $withBackoffTime = $this->getOperationExecutionTime($session, $operation);

        self::assertLessThan(0.3, abs($withBackoffTime - ($noBackoffTime + 0.3)));
    }

    private function getOperationExecutionTime(Session $session, WithTransaction $operation): float
    {
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => 'alwaysOn',
            'data' => [
                'failCommands' => ['insert'],
                'errorCode' => 2,
                'errorLabels' => ['SystemOverloadedError', 'RetryableError'],
            ],
        ]);

        $start = hrtime(true);

        try {
            $operation->execute($session);
            $this->fail('Expected exception was not thrown');
        } catch (ServerException) {
            // Expected Exception due to failCommand, ignore
        }

        // Return duration in seconds
        return (hrtime(true) - $start) / 1e9;
    }
}
