<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Count;
use MongoDB\Tests\CommandObserver;
use stdClass;

class CountFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new Count(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectNotHasAttribute('readConcern', $command);
            }
        );
    }
}
