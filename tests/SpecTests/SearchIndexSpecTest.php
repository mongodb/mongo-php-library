<?php

namespace MongoDB\Tests\SpecTests;

use Closure;
use MongoDB\Collection;
use MongoDB\Model\CachingIterator;
use MongoDB\Tests\FunctionalTestCase;

use function bin2hex;
use function count;
use function hrtime;
use function iterator_to_array;
use function json_encode;
use function random_bytes;
use function sleep;
use function sprintf;

use const JSON_THROW_ON_ERROR;

/**
 * Functional tests for the Atlas Search index management.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/index-management/index-management.rst
 * @group atlas
 */
class SearchIndexSpecTest extends FunctionalTestCase
{
    private const WAIT_TIMEOUT = 300; // 5 minutes

    public function setUp(): void
    {
        if (! self::isAtlas()) {
            self::markTestSkipped('Search Indexes are only supported on MongoDB Atlas');
        }

        parent::setUp();
    }

    public function testCreateAndListSearchIndexes(): void
    {
        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());
        $name = 'test-search-index';
        $mapping = ['mappings' => ['dynamic' => false]];

        $createdName = $collection->createSearchIndex(
            $mapping,
            ['name' => $name, 'comment' => 'Index creation test'],
        );
        $this->assertSame($name, $createdName);

        [$index] = $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));

        $this->assertSame($name, $index->name);

        // Convert to JSON to compare nested associative arrays and nested objects
        $this->assertJsonStringEqualsJsonString(
            json_encode($mapping, JSON_THROW_ON_ERROR),
            json_encode($index->latestDefinition, JSON_THROW_ON_ERROR),
        );
    }

    public function testCreateMultipleIndexesInBatch(): void
    {
        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());
        $names = ['test-search-index-1', 'test-search-index-2'];
        $mapping = ['mappings' => ['dynamic' => false]];

        $createdNames = $collection->createSearchIndexes([
            ['name' => $names[0], 'definition' => $mapping],
            ['name' => $names[1], 'definition' => $mapping],
        ]);
        $this->assertSame($names, $createdNames);

        $indexes = $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));

        foreach ($names as $key => $name) {
            $index = $indexes[$key];
            $this->assertSame($name, $index->name);

            // Convert to JSON to compare nested associative arrays and nested objects
            $this->assertJsonStringEqualsJsonString(
                json_encode($mapping, JSON_THROW_ON_ERROR),
                json_encode($index->latestDefinition, JSON_THROW_ON_ERROR),
            );
        }
    }

    public function testDropSearchIndexes(): void
    {
        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());
        $name = 'test-search-index';
        $mapping = ['mappings' => ['dynamic' => false]];

        $createdName = $collection->createSearchIndex(
            $mapping,
            ['name' => $name],
        );
        $this->assertSame($name, $createdName);

        $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));

        $collection->dropSearchIndex($name);

        $indexes = $this->waitForIndexes($collection, fn (array $indexes): bool => count($indexes) === 0);

        $this->assertCount(0, $indexes);
    }

    public function testUpdateSearchIndex(): void
    {
        $collection = $this->createCollection($this->getDatabaseName(), $this->getCollectionName());
        $name = 'test-search-index';
        $mapping = ['mappings' => ['dynamic' => false]];

        $createdName = $collection->createSearchIndex(
            $mapping,
            ['name' => $name],
        );
        $this->assertSame($name, $createdName);

        $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));

        $mapping = ['mappings' => ['dynamic' => true]];
        $collection->updateSearchIndex($name, $mapping);

        [$index] = $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));

        $this->assertSame($name, $index->name);

        // Convert to JSON to compare nested associative arrays and nested objects
        $this->assertJsonStringEqualsJsonString(
            json_encode($mapping, JSON_THROW_ON_ERROR),
            json_encode($index->latestDefinition, JSON_THROW_ON_ERROR),
        );
    }

    public function testDropSearchIndexSuppressNamespaceNotFoundError(): void
    {
        $collection = $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());

        $collection->dropSearchIndex('test-seach-index');

        $this->expectNotToPerformAssertions();
    }

    /**
     * Randomize the collection name to avoid duplicate index names when running tests concurrently.
     * Search index operations are asynchronous and can take up to a few minutes.
     */
    protected function getCollectionName(): string
    {
        return sprintf('%s.%s', parent::getCollectionName(), bin2hex(random_bytes(5)));
    }

    private function waitForIndexes(Collection $collection, Closure $callback): array
    {
        $timeout = hrtime()[0] + self::WAIT_TIMEOUT;
        while (hrtime()[0] < $timeout) {
            sleep(5);
            $result = $collection->listSearchIndexes();
            $this->assertInstanceOf(CachingIterator::class, $result);
            $result = iterator_to_array($result);
            if ($callback($result)) {
                return $result;
            }
        }

        $this->fail('Operation did not complete in time');
    }

    private function allIndexesAreQueryable(array $indexes): bool
    {
        if (count($indexes) === 0) {
            return false;
        }

        foreach ($indexes as $index) {
            if (! $index->queryable) {
                return false;
            }

            if (! $index->status === 'READY') {
                return false;
            }
        }

        return true;
    }
}
