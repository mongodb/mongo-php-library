<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\ListDatabases;
use MongoDB\Tests\CommandObserver;
use stdClass;

class ListDatabasesFunctionalTest extends FunctionalTestCase
{
    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new ListDatabases(
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectHasAttribute('lsid', $command);
            }
        );
    }
}
