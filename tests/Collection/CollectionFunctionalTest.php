<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Driver\BulkWrite;

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

    public function testFindOne()
    {
        $this->createFixtures(5);

        $filter = array('_id' => array('$lt' => 5));
        $options = array(
            'skip' => 1,
            'sort' => array('x' => -1),
        );

        $expected = array('_id' => 3, 'x' => 33);

        $this->assertSame($expected, $this->collection->findOne($filter, $options));
    }

    /**
     * Create data fixtures.
     *
     * @param integer $n
     */
    private function createFixtures($n)
    {
        $bulkWrite = new BulkWrite(true);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert(array(
                '_id' => $i,
                'x' => (integer) ($i . $i),
            ));
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
