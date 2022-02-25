<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\DatabaseCommand;
use MongoDB\Tests\CommandObserver;

class DatabaseCommandFunctionalTest extends FunctionalTestCase
{
    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new DatabaseCommand(
                    $this->getDatabaseName(),
                    ['ping' => 1],
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
