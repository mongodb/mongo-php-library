<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Driver\Server;

use function array_unique;
use function count;

/**
 * Transactions spec prose tests.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/transactions/tests/README.rst#mongos-pinning-prose-tests
 */
class TransactionsSpecTest extends FunctionalTestCase
{
    /**
     * Prose test 1: Test that starting a new transaction on a pinned
     * ClientSession unpins the session and normal server selection is performed
     * for the next operation.
     */
    public function testStartingNewTransactionOnPinnedSessionUnpinsSession(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        if (! $this->isMongos()) {
            $this->markTestSkipped('Pinning tests require mongos');
        }

        $client = self::createTestClient(static::getUri(true));

        $session = $client->startSession();
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        // Create collection before transaction
        $collection->insertOne([]);

        $session->startTransaction([]);
        $collection->insertOne([], ['session' => $session]);
        $session->commitTransaction();

        $servers = [];
        for ($i = 0; $i < 50; $i++) {
            $session->startTransaction([]);
            $cursor = $collection->find([], ['session' => $session]);
            $servers[] = $cursor->getServer()->getHost() . ':' . $cursor->getServer()->getPort();
            $this->assertInstanceOf(Server::class, $session->getServer());
            $session->commitTransaction();
        }

        $servers = array_unique($servers);
        $this->assertGreaterThan(1, count($servers));

        $session->endSession();
    }

    /**
     * Prose test 2: Test non-transaction operations using a pinned
     * ClientSession unpins the session and normal server selection is
     * performed.
     */
    public function testRunningNonTransactionOperationOnPinnedSessionUnpinsSession(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        if (! $this->isMongos()) {
            $this->markTestSkipped('Pinning tests require mongos');
        }

        $client = self::createTestClient(static::getUri(true));

        $session = $client->startSession();
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        // Create collection before transaction
        $collection->insertOne([]);

        $session->startTransaction([]);
        $collection->insertOne([], ['session' => $session]);
        $session->commitTransaction();

        $servers = [];
        for ($i = 0; $i < 50; $i++) {
            $cursor = $collection->find([], ['session' => $session]);
            $servers[] = $cursor->getServer()->getHost() . ':' . $cursor->getServer()->getPort();
            $this->assertNull($session->getServer());
        }

        $servers = array_unique($servers);
        $this->assertGreaterThan(1, count($servers));

        $session->endSession();
    }
}
