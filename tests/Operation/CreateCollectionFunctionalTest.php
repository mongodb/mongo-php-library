<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\CreateCollection;
use MongoDB\Tests\CommandObserver;
use stdClass;

class CreateCollectionFunctionalTest extends FunctionalTestCase
{
    public function testDefaultWriteConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new CreateCollection(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectNotHasAttribute('writeConcern', $command);
            }
        );
    }
}
