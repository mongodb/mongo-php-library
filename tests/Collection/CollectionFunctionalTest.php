<?php

namespace MongoDB\Tests\Collection;

/**
 * Functional tests for the Collection class.
 */
class CollectionFunctionalTest extends FunctionalTestCase
{
    public function testDrop()
    {
        $writeResult = $this->collection->insertOne(array('x' => 1));
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->collection->drop();
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }
}
