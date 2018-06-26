<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Distinct;
use MongoDB\Tests\CommandObserver;
use stdClass;

class DistinctFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
                    [],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(array $event) {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
                    [],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }
}
