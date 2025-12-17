<?php

namespace Exception;

use MongoDB\Collection;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Exception\AtlasSearchNotSupportedException;
use MongoDB\Tests\Collection\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;

class AtlasSearchNotSupportedExceptionTest extends FunctionalTestCase
{
    #[DoesNotPerformAssertions]
    public function testListSearchIndexesNotSupportedException(): void
    {
        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        try {
            $collection->listSearchIndexes();
        } catch (AtlasSearchNotSupportedException) {
            // If an exception is thrown because Atlas Search is not supported,
            // then the test is successful because it has the correct exception class.
        }
    }

    #[DoesNotPerformAssertions]
    public function testCreateSearchIndexNotSupportedException(): void
    {
        $collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        try {
            $collection->createSearchIndex(['mappings' => ['dynamic' => false]], ['name' => 'test-search-index']);
        } catch (AtlasSearchNotSupportedException) {
            // If an exception is thrown because Atlas Search is not supported,
            // then the test is successful because it has the correct exception class.
        }
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
