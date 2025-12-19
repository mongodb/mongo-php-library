<?php

namespace MongoDB\Tests\Exception;

use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Exception\SearchNotSupportedException;
use MongoDB\Tests\Collection\FunctionalTestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;

class SearchNotSupportedExceptionTest extends FunctionalTestCase
{
    #[DoesNotPerformAssertions]
    public function testListSearchIndexesNotSupportedException(): void
    {
        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        try {
            $collection->listSearchIndexes();
        } catch (SearchNotSupportedException) {
            // If an exception is thrown because Atlas Search is not supported,
            // then the test is successful because it has the correct exception class.
        }
    }

    #[DoesNotPerformAssertions]
    public function testSearchIndexManagementNotSupportedException(): void
    {
        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        try {
            $collection->createSearchIndex(['mappings' => ['dynamic' => false]], ['name' => 'test-search-index']);
        } catch (SearchNotSupportedException) {
            // If an exception is thrown because Atlas Search is not supported,
            // then the test is successful because it has the correct exception class.
        }

        try {
            $collection->updateSearchIndex('test-search-index', ['mappings' => ['dynamic' => true]]);
        } catch (SearchNotSupportedException) {
            // If an exception is thrown because Atlas Search is not supported,
            // then the test is successful because it has the correct exception class.
        }

        try {
            $collection->dropSearchIndex('test-search-index');
        } catch (SearchNotSupportedException) {
            // If an exception is thrown because Atlas Search is not supported,
            // then the test is successful because it has the correct exception class.
        }
    }

    public function testOtherStageNotFound(): void
    {
        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());

        try {
            $collection->aggregate([
                ['$searchStageNotExisting' => ['text' => ['query' => 'test', 'path' => 'field']]],
            ]);
            self::fail('Expected ServerException was not thrown');
        } catch (ServerException $exception) {
            self::assertNotInstanceOf(SearchNotSupportedException::class, $exception, $exception);
        }
    }

    public function testOtherCommandNotFound(): void
    {
        try {
            $this->manager->executeCommand($this->getDatabaseName(), new Command(['nonExistingCommand' => 1]));
            self::fail('Expected ServerException was not thrown');
        } catch (ServerException $exception) {
            self::assertFalse(SearchNotSupportedException::isSearchNotSupportedError($exception));
        }
    }
}
