<?php

namespace Exception;

use MongoDB\Collection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Exception\AtlasSearchNotSupportedException;
use MongoDB\Tests\Collection\FunctionalTestCase;

class AtlasSearchNotSupportedExceptionTest extends FunctionalTestCase
{
    public function testListSearchIndexesNotSupportedException(): void
    {
        if (self::isAtlas()) {
            self::markTestSkipped('Atlas Search is supported on Atlas');
        }

        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $this->expectException(AtlasSearchNotSupportedException::class);

        $collection->listSearchIndexes();
    }

    public function testCreateSearchIndexNotSupportedException(): void
    {
        if (self::isAtlas()) {
            self::markTestSkipped('Atlas Search is supported on Atlas');
        }

        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $this->expectException(AtlasSearchNotSupportedException::class);

        $collection->createSearchIndex(['mappings' => ['dynamic' => false]], ['name' => 'test-search-index']);
    }

    public function testOtherStageNotFound(): void
    {
        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        try {
            $collection->aggregate([
                ['$searchStageNotExisting' => ['text' => ['query' => 'test', 'path' => 'field']]],
            ]);
            self::fail('Expected ServerException was not thrown');
        } catch (ServerException $exception) {
            self::assertNotInstanceOf(AtlasSearchNotSupportedException::class, $exception, $exception);
        }
    }

    public function testOtherCommandNotFound(): void
    {
        try {
            $this->manager->executeCommand($this->getDatabaseName(), new Command(['nonExistingCommand' => 1]));
            self::fail('Expected ServerException was not thrown');
        } catch (ServerException $exception) {
            self::assertFalse(AtlasSearchNotSupportedException::isAtlasSearchNotSupportedError($exception));
        }
    }
}
