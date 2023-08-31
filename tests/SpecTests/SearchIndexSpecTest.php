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
use function random_bytes;
use function sleep;
use function sprintf;

/**
 * Functional tests for the Atlas Search index management.
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/index-management/tests/README.rst#search-index-management-helpers
 * @group atlas
 */
class SearchIndexSpecTest extends FunctionalTestCase
{
    private const WAIT_TIMEOUT_SEC = 300;
    private const STATUS_READY = 'READY';

    public function setUp(): void
    {
        if (! self::isAtlas()) {
            self::markTestSkipped('Search Indexes are only supported on MongoDB Atlas 7.0+');
        }

        parent::setUp();

        $this->skipIfServerVersion('<', '7.0', 'Search Indexes are only supported on MongoDB Atlas 7.0+');
    }

    /**
     * Case 1: Driver can successfully create and list search indexes
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/index-management/tests/README.rst#case-1-driver-can-successfully-create-and-list-search-indexes
     */
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

        $indexes = $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));

        $this->assertCount(1, $indexes);
        $this->assertSame($name, $indexes[0]->name);
        $this->assertSameDocument($mapping, $indexes[0]->latestDefinition);
    }

    /**
     * Case 2: Driver can successfully create multiple indexes in batch
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/index-management/tests/README.rst#case-2-driver-can-successfully-create-multiple-indexes-in-batch
     */
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

        $this->assertCount(2, $indexes);
        foreach ($names as $key => $name) {
            $index = $indexes[$key];
            $this->assertSame($name, $index->name);
            $this->assertSameDocument($mapping, $index->latestDefinition);
        }
    }

    /**
     * Case 3: Driver can successfully drop search indexes
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/index-management/tests/README.rst#case-3-driver-can-successfully-drop-search-indexes
     */
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

        $indexes = $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));
        $this->assertCount(1, $indexes);

        $collection->dropSearchIndex($name);

        $indexes = $this->waitForIndexes($collection, fn (array $indexes): bool => count($indexes) === 0);
        $this->assertCount(0, $indexes);
    }

    /**
     * Case 4: Driver can update a search index
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/index-management/tests/README.rst#case-4-driver-can-update-a-search-index
     */
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

        $indexes = $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));
        $this->assertCount(1, $indexes);

        $mapping = ['mappings' => ['dynamic' => true]];
        $collection->updateSearchIndex($name, $mapping);

        $indexes = $this->waitForIndexes($collection, fn ($indexes) => $this->allIndexesAreQueryable($indexes));

        $this->assertCount(1, $indexes);
        $this->assertSame($name, $indexes[0]->name);
        $this->assertSameDocument($mapping, $indexes[0]->latestDefinition);
    }

    /**
     * Case 5: dropSearchIndex suppresses namespace not found errors
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/index-management/tests/README.rst#case-5-dropsearchindex-suppresses-namespace-not-found-errors
     */
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
        $timeout = hrtime()[0] + self::WAIT_TIMEOUT_SEC;
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

            if (! $index->status === self::STATUS_READY) {
                return false;
            }
        }

        return true;
    }
}
