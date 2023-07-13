<?php

namespace MongoDB\Tests\Model;

use MongoDB\Model\CachingIterator;
use MongoDB\Tests\FunctionalTestCase;

class CachingIteratorFunctionalTest extends FunctionalTestCase
{
    /** @see https://jira.mongodb.org/browse/PHPLIB-1167 */
    public function testEmptyCursor(): void
    {
        $collection = $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
        $cursor = $collection->find();
        $iterator = new CachingIterator($cursor);

        $this->assertSame(0, $iterator->count());
        $iterator->rewind();
        $this->assertFalse($iterator->valid());
        $this->assertNull($iterator->current());
        $this->assertNull($iterator->key());
    }

    public function testCursor(): void
    {
        $collection = $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
        $collection->insertOne(['_id' => 1]);
        $collection->insertOne(['_id' => 2]);
        $cursor = $collection->find();
        $iterator = new CachingIterator($cursor);

        $this->assertSame(2, $iterator->count());

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertNotNull($iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertNotNull($iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
        $this->assertNull($iterator->current());
        $this->assertNull($iterator->key());
    }
}
