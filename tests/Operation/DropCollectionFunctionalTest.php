<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\DropCollection;
use MongoDB\Operation\InsertOne;
use MongoDB\Tests\CommandObserver;
use PHPUnit\Framework\Attributes\Depends;

class DropCollectionFunctionalTest extends FunctionalTestCase
{
    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new DropCollection(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['writeConcern' => $this->createDefaultWriteConcern()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasProperty('writeConcern', $event['started']->getCommand());
            },
        );
    }

    public function testDropExistingCollection(): void
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($server);

        $this->assertCollectionDoesNotExist($this->getCollectionName());
    }

    #[Depends('testDropExistingCollection')]
    public function testDropNonexistentCollection(): void
    {
        $this->assertCollectionDoesNotExist($this->getCollectionName());

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new DropCollection(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
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
