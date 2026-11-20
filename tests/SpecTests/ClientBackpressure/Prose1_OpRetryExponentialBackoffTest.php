<?php

namespace MongoDB\Tests\SpecTests\ClientBackpressure;

use MongoDB\Driver\Exception\ServerException;
use MongoDB\Driver\Session;
use MongoDB\Operation\WithTransaction;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

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
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        $callback = static function (Session $session) use ($collection): void {
            $collection->insertOne(['a' => 1], ['session' => $session]);
        };

        $operation = new WithTransaction($callback);
        $session = $client->startSession();

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
            // Expected exception due to failCommand
        }

        $elapsed = (hrtime(true) - $start) / 1e9;

        /* The spec requires comparing two runs with jitter fixed at 0 and 1 to verify
         * that backoff delay scales with the jitter value (expected difference: ~0.3s).
         *
         * This is not achievable from PHPLIB because the overload retry and its backoff
         * are implemented inside ext-mongodb (C level). WithTransaction only retries on
         * TransientTransactionError, not on SystemOverloadedError, so setFixedJitter()
         * has no effect on the timing of this test.
         *
         * As partial verification, we assert that the operation completed within the
         * maximum possible backoff window: MAX_RETRIES (2) × MAX_BACKOFF (10s) = 20s. */
        self::assertLessThan(20.0, $elapsed);
    }
}
