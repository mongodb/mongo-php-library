<?php

namespace MongoDB\Tests\Database;

use MongoDB\Driver\BulkWrite;
use MongoDB\Model\CollectionInfo;
use MongoDB\Model\CollectionInfoIterator;

/**
 * Functional tests for collection management methods.
 */
class CollectionManagementFunctionalTest extends FunctionalTestCase
{
    public function testCreateCollection(): void
    {
        $that = $this;
        $basicCollectionName = $this->getCollectionName() . '.basic';

        $commandResult = $this->database->createCollection($basicCollectionName);
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionExists($basicCollectionName, null, function (CollectionInfo $info) use ($that): void {
            $that->assertFalse($info->isCapped());
        });

        $cappedCollectionName = $this->getCollectionName() . '.capped';
        $cappedCollectionOptions = [
            'capped' => true,
            'max' => 100,
            'size' => 1048576,
        ];

        $commandResult = $this->database->createCollection($cappedCollectionName, $cappedCollectionOptions);
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionExists($cappedCollectionName, null, function (CollectionInfo $info) use ($that): void {
            $that->assertTrue($info->isCapped());
            $that->assertEquals(100, $info->getCappedMax());
            $that->assertEquals(1048576, $info->getCappedSize());
        });
    }

    public function testDropCollection(): void
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->insert(['x' => 1]);

        $writeResult = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->database->dropCollection($this->getCollectionName());
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    public function testListCollections(): void
    {
        $commandResult = $this->database->createCollection($this->getCollectionName());
        $this->assertCommandSucceeded($commandResult);

        $collections = $this->database->listCollections();
        $this->assertInstanceOf(CollectionInfoIterator::class, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInfo::class, $collection);
        }
    }

    public function testListCollectionsWithFilter(): void
    {
        $commandResult = $this->database->createCollection($this->getCollectionName());
        $this->assertCommandSucceeded($commandResult);

        $collectionName = $this->getCollectionName();
        $options = ['filter' => ['name' => $collectionName]];

        $collections = $this->database->listCollections($options);
        $this->assertInstanceOf(CollectionInfoIterator::class, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInfo::class, $collection);
            $this->assertEquals($collectionName, $collection->getName());
        }
    }

    public function testListCollectionNames(): void
    {
        $commandResult = $this->database->createCollection($this->getCollectionName());
        $this->assertCommandSucceeded($commandResult);

        $collections = $this->database->listCollectionNames();

        foreach ($collections as $collection) {
            $this->assertIsString($collection);
        }
    }

    public function testListCollectionNamesWithFilter(): void
    {
        $commandResult = $this->database->createCollection($this->getCollectionName());
        $this->assertCommandSucceeded($commandResult);

        $collectionName = $this->getCollectionName();
        $options = ['filter' => ['name' => $collectionName]];

        $collections = $this->database->listCollectionNames($options);

        foreach ($collections as $collection) {
            $this->assertEquals($collectionName, $collection);
        }
    }
}
