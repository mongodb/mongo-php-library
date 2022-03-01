<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Model\IndexInfo;
use MongoDB\Model\IndexInfoIterator;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListIndexes;
use MongoDB\Tests\CommandObserver;

class ListIndexesFunctionalTest extends FunctionalTestCase
{
    public function testListIndexesForNewlyCreatedCollection(): void
    {
        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($this->getPrimaryServer());
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        $indexes = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(IndexInfoIterator::class, $indexes);

        $this->assertCount(1, $indexes);

        foreach ($indexes as $index) {
            $this->assertInstanceOf(IndexInfo::class, $index);
            $this->assertEquals(['_id' => 1], $index->getKey());
            $this->assertSame($this->getNamespace(), $index->getNamespace());
        }
    }

    public function testListIndexesForNonexistentCollection(): void
    {
        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());

        $operation = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        $indexes = $operation->execute($this->getPrimaryServer());

        $this->assertCount(0, $indexes);
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new ListIndexes(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }
}
