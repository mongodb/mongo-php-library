<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CreateCollection;
use MongoDB\Tests\CommandObserver;

class CreateCollectionFunctionalTest extends FunctionalTestCase
{
    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new CreateCollection(
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

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new CreateCollection(
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
