<?php

namespace MongoDB\Tests\Operation;

use Iterator;
use MongoDB\Model\CollectionInfo;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListCollections;
use MongoDB\Tests\CommandObserver;
use PHPUnit\Framework\Attributes\Group;

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

        $this->assertInstanceOf(Iterator::class, $collections);

        $this->assertCount(1, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInfo::class, $collection);
            $this->assertEquals($this->getCollectionName(), $collection->getName());
        }
    }

    #[Group('matrix-testing-exclude-server-4.4-driver-4.0')]
    #[Group('matrix-testing-exclude-server-4.4-driver-4.2')]
    #[Group('matrix-testing-exclude-server-5.0-driver-4.0')]
    #[Group('matrix-testing-exclude-server-5.0-driver-4.2')]
    public function testIdIndexAndInfo(): void
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListCollections($this->getDatabaseName(), ['filter' => ['name' => $this->getCollectionName()]]);
        $collections = $operation->execute($server);

        $this->assertInstanceOf(Iterator::class, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInfo::class, $collection);
            $this->assertArrayHasKey('readOnly', $collection['info']);
            // Use assertMatchesDocument as MongoDB 4.0 and 4.2 include a ns field
            // TODO: change to assertEquals when dropping support for MongoDB 4.2
            $this->assertMatchesDocument(['v' => 2, 'key' => ['_id' => 1], 'name' => '_id_'], $collection['idIndex']);
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

    public function testAuthorizedCollectionsOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new ListCollections(
                    $this->getDatabaseName(),
                    ['authorizedCollections' => true],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasProperty('authorizedCollections', $event['started']->getCommand());
                $this->assertSame(true, $event['started']->getCommand()->authorizedCollections);
            },
        );
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new ListCollections(
                    $this->getDatabaseName(),
                    ['session' => $this->createSession()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasProperty('lsid', $event['started']->getCommand());
            },
        );
    }
}
