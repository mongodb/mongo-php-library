<?php

namespace MongoDB\Tests;

use MongoDB\CachingIterator;

class CachingIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sanity check for all following tests
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot traverse an already closed generator
     */
    public function testTraverseGeneratorConsumesIt()
    {
        $iterator = $this->getTraversable([1, 2, 3]);
        $this->assertSame([1, 2, 3], iterator_to_array($iterator));
        $this->assertSame([1, 2, 3], iterator_to_array($iterator));
    }

    public function testIterateOverItems()
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));

        $expectedKey = 0;
        $expectedItem = 1;
        foreach ($iterator as $key => $item) {
            $this->assertSame($expectedKey++, $key);
            $this->assertSame($expectedItem++, $item);
        }
        $this->assertFalse($iterator->valid());
    }

    public function testIteratePartiallyThenRewind()
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));

        $this->assertSame(1, $iterator->current());
        $iterator->next();

        $this->assertSame([1, 2, 3], iterator_to_array($iterator));
    }

    public function testCount()
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));
        $this->assertCount(3, $iterator);
    }

    public function testCountAfterPartiallyIterating()
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));
        $iterator->next();
        $this->assertCount(3, $iterator);
    }

    private function getTraversable($items)
    {
        foreach ($items as $item) {
            yield $item;
        }
    }
}
