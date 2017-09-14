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
            function(stdClass $command) {
                $this->assertObjectNotHasAttribute('readConcern', $command);
            }
        );
    }
}
