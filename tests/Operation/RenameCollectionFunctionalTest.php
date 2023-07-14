<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\Exception\CommandException;
use MongoDB\Operation\FindOne;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\RenameCollection;
use MongoDB\Tests\CommandObserver;

class RenameCollectionFunctionalTest extends FunctionalTestCase
{
    private static int $errorCodeNamespaceNotFound = 26;

    private static int $errorCodeNamespaceExists = 48;

    private string $toCollectionName;

    public function setUp(): void
    {
        parent::setUp();

        $this->toCollectionName = $this->getCollectionName() . '.renamed';
        $this->dropCollection($this->getDatabaseName(), $this->toCollectionName);
        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
    }

    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $server = $this->getPrimaryServer();

                $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
                $writeResult = $insertOne->execute($server);
                $this->assertEquals(1, $writeResult->getInsertedCount());

                $operation = new RenameCollection(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    $this->getDatabaseName(),
                    $this->toCollectionName,
                    ['writeConcern' => $this->createDefaultWriteConcern()],
                );

                $operation->execute($server);
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            },
        );
    }

    public function testRenameCollectionToNonexistentTarget(): void
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['_id' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new RenameCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            $this->getDatabaseName(),
            $this->toCollectionName,
        );
        $commandResult = $operation->execute($server);

        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionDoesNotExist($this->getCollectionName());
        $this->assertCollectionExists($this->toCollectionName);

        $operation = new FindOne($this->getDatabaseName(), $this->toCollectionName, []);
        $this->assertSameDocument(['_id' => 1], $operation->execute($server));
    }

    public function testRenameCollectionExistingTarget(): void
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['_id' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $insertOne = new InsertOne($this->getDatabaseName(), $this->toCollectionName, ['_id' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $this->expectException(CommandException::class);

        // TODO: mongos returns an inconsistent error code (see: SERVER-60632)
        if (! $this->isShardedCluster()) {
            $this->expectExceptionCode(self::$errorCodeNamespaceExists);
        }

        $operation = new RenameCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            $this->getDatabaseName(),
            $this->toCollectionName,
        );
        $operation->execute($server);
    }

    public function testRenameNonexistentCollection(): void
    {
        $this->expectException(CommandException::class);
        $this->expectExceptionCode(self::$errorCodeNamespaceNotFound);

        $operation = new RenameCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            $this->getDatabaseName(),
            $this->toCollectionName,
        );
        $operation->execute($this->getPrimaryServer());
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $server = $this->getPrimaryServer();

                $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
                $writeResult = $insertOne->execute($server);
                $this->assertEquals(1, $writeResult->getInsertedCount());

                $operation = new RenameCollection(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    $this->getDatabaseName(),
                    $this->toCollectionName,
                    ['session' => $this->createSession()],
                );

                $operation->execute($server);
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            },
        );
    }
}
