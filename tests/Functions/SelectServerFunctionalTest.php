<?php

namespace MongoDB\Tests\Functions;

use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Tests\FunctionalTestCase;

use function MongoDB\select_server;

class SelectServerFunctionalTest extends FunctionalTestCase
{
    /** @dataProvider providePinnedOptions */
    public function testSelectServerPrefersPinnedServer(array $options): void
    {
        $this->skipIfTransactionsAreNotSupported();

        if (! $this->isShardedCluster()) {
            $this->markTestSkipped('Pinning requires a sharded cluster');
        }

        if ($this->isLoadBalanced()) {
            $this->markTestSkipped('libmongoc does not pin for load-balanced topology');
        }

        /* By default, the Manager under test is created with a single-mongos
         * URI. Explicitly create a Client with multiple mongoses. */
        $client = static::createTestClient(static::getUri(true));

        // Collection must be created before the transaction starts
        $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        $session = $client->startSession();
        $session->startTransaction();

        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection->find([], ['session' => $session]);

        $this->assertTrue($session->isInTransaction());
        $this->assertInstanceOf(Server::class, $session->getServer(), 'Session is pinned');
        $this->assertEquals($session->getServer(), select_server($client->getManager(), ['session' => $session]));
    }

    public static function providePinnedOptions(): array
    {
        return [
            [['readPreference' => new ReadPreference(ReadPreference::PRIMARY_PREFERRED)]],
            [[]],
        ];
    }
}
