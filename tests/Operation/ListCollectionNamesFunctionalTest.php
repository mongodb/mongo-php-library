<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListCollectionNames;
use MongoDB\Tests\CommandObserver;

use function version_compare;

class ListCollectionNamesFunctionalTest extends FunctionalTestCase
{
    public function testListCollectionNamesForNewlyCreatedDatabase(): void
    {
        $server = $this->getPrimaryServer();

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListCollectionNames($this->getDatabaseName(), ['filter' => ['name' => $this->getCollectionName()]]);
        $names = $operation->execute($server);
        $this->assertCount(1, $names);

        foreach ($names as $collection) {
            $this->assertSame($this->getCollectionName(), $collection);
        }
    }

    public function testSessionOption(): void
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function (): void {
                $operation = new ListCollectionNames(
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
