<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Aggregate;

class AggregateFunctionalTest extends FunctionalTestCase
{
    /**
     * @expectedException MongoDB\Driver\Exception\RuntimeException
     */
    public function testUnrecognizedPipelineState()
    {
        $server = $this->getPrimaryServer();
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [['$foo' => 1]]);
        $operation->execute($server);
    }
}
