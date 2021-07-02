<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Model\CollectionInfo;
use MongoDB\Model\CollectionInfoIterator;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListCollections;
use MongoDB\Tests\CommandObserver;

use function version_compare;

class ListCollectionsFunctionalTest extends FunctionalTestCase
{
    public function testListCollectionsForNewlyCreatedDatabase(): void
    {
        $server = $this->getPrimaryServer();

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListCollections($this->getDatabaseName(), ['filter' => ['name' => $this->getCollectionName()]]);
        $collections = $operation->execute($server);

        $this->assertInstanceOf(CollectionInfoIterator::class, $collections);

        $this->assertCount(1, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInfo::class, $collection);
            $this->assertEquals($this->getCollectionName(), $collection->getName());
        }
    }

    /**
     * @group matrix-testing-exclude-server-4.4-driver-4.0
     * @group matrix-testing-exclude-server-4.4-driver-4.2
     * @group matrix-testing-exclude-server-5.0-driver-4.0
     * @group matrix-testing-exclude-server-5.0-driver-4.2
     */
    public function testIdIndexAndInfo(): void
    {
        if (version_compare($this->getServerVersion(), '3.4.0', '<')) {
            $this->markTestSkipped('idIndex and info are not supported');
        }

        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListCollections($this->getDatabaseName(), ['filter' => ['name' => $this->getCollectionName()]]);
        $collections = $operation->execute($server);

        $this->assertInstanceOf(CollectionInfoIterator::class, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInfo::class, $collection);
            $this->assertArrayHasKey('readOnly', $collection['info']);
            $this->assertEquals(['v' => 2, 'key' => ['_id' => 1], 'name' => '_id_', 'ns' => $this->getNamespace()], $collection['idIndex']);
        }
    }

    public function testListCollectionsForNonexistentDatabase(): void
    {
        $server = $this->getPrimaryServer();

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);

        $operation = new ListCollections($this->getDatabaseName());
        $collections = $operation->execute($server);

        $this->assertCount(0, $collections);
    }

    public function testSessionOption(): void
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function (): void {
                $operation = new ListCollections(
                    $this->getDatabaseName(),
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }
}
