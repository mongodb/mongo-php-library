<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\DatabaseCommand;
use MongoDB\Tests\CommandObserver;
use function version_compare;

class DatabaseCommandFunctionalTest extends FunctionalTestCase
{
    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function () {
                $operation = new DatabaseCommand(
                    $this->getDatabaseName(),
                    ['ping' => 1],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }
}
