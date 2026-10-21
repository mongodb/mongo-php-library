<?php

namespace MongoDB\Tests\Database;

use Iterator;
use MongoDB\Driver\BulkWrite;
use MongoDB\Model\CollectionInfo;

/**
 * Functional tests for collection management methods.
 */
class CollectionManagementFunctionalTest extends FunctionalTestCase
{
    public function testCreateCollection(): void
    {
        $that = $this;
        $basicCollectionName = $this->getCollectionName() . '.basic';

        $this->database->createCollection($basicCollectionName);
        $this->assertCollectionExists($basicCollectionName, null, function (CollectionInfo $info) use ($that): void {
            $that->assertFalse($info->isCapped());
        });

        $cappedCollectionName = $this->getCollectionName() . '.capped';
        $cappedCollectionOptions = [
            'capped' => true,
            'max' => 100,
            'size' => 1_048_576,
        ];

        $this->database->createCollection($cappedCollectionName, $cappedCollectionOptions);
        $this->assertCollectionExists($cappedCollectionName, null, function (CollectionInfo $info) use ($that): void {
            $that->assertTrue($info->isCapped());
            $that->assertEquals(100, $info->getCappedMax());
            $that->assertEquals(1_048_576, $info->getCappedSize());
        });
    }

    public function testDropCollection(): void
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->insert(['x' => 1]);

        $writeResult = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $this->database->dropCollection($this->getCollectionName());
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    public function testListCollections(): void
    {
        $this->database->createCollection($this->getCollectionName());

        $collections = $this->database->listCollections();
        $this->assertInstanceOf(Iterator::class, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInfo::class, $collection);
        }
    }

    public function testListCollectionsWithFilter(): void
    {
        $this->database->createCollection($this->getCollectionName());

        $collectionName = $this->getCollectionName();
        $options = ['filter' => ['name' => $collectionName]];

        $collections = $this->database->listCollections($options);
        $this->assertInstanceOf(Iterator::class, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInfo::class, $collection);
            $this->assertEquals($collectionName, $collection->getName());
        }
    }

    public function testListCollectionNames(): void
    {
        $this->database->createCollection($this->getCollectionName());

        $collections = $this->database->listCollectionNames();

        foreach ($collections as $collection) {
            $this->assertIsString($collection);
        }
    }

    public function testListCollectionNamesWithFilter(): void
    {
        $this->database->createCollection($this->getCollectionName());

        $collectionName = $this->getCollectionName();
        $options = ['filter' => ['name' => $collectionName]];

        $collections = $this->database->listCollectionNames($options);

        foreach ($collections as $collection) {
            $this->assertEquals($collectionName, $collection);
        }
    }
}
