<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\Server;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\ListIndexes;

class ListIndexesFunctionalTest extends FunctionalTestCase
{
    public function testListIndexesForNewlyCreatedCollection()
    {
        $server = $this->getPrimaryServer();

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($server);

        $writeResult = $this->manager->executeInsert($this->getNamespace(), ['x' => 1]);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        // Convert the CursorInfoIterator to an array since we cannot rewind its cursor
        $indexes = iterator_to_array($operation->execute($server));

        $this->assertCount(1, $indexes);

        foreach ($indexes as $index) {
            $this->assertInstanceOf('MongoDB\Model\IndexInfo', $index);
            $this->assertEquals(['_id' => 1], $index->getKey());
        }
    }

    public function testListIndexesForNonexistentCollection()
    {
        $server = $this->getPrimaryServer();

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($server);

        $operation = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        $indexes = $operation->execute($server);

        $this->assertCount(0, $indexes);
    }
}
