<?php

namespace MongoDB\Tests\Model;

use MongoDB\Model\CachingIterator;
use MongoDB\Tests\TestCase;
use Exception;

class CachingIteratorTest extends TestCase
{
    /**
     * Sanity check for all following tests.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot traverse an already closed generator
     */
    public function testTraversingGeneratorConsumesIt()
    {
        $iterator = $this->getTraversable([1, 2, 3]);
        $this->assertSame([1, 2, 3], iterator_to_array($iterator));
        $this->assertSame([1, 2, 3], iterator_to_array($iterator));
    }

    public function testConstructorRewinds()
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));

        $this->assertTrue($iterator->valid());
        $this->assertSame(0, $iterator->key());
        $this->assertSame(1, $iterator->current());
    }

    public function testIteration()
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

    public function testIterationWithEmptySet()
    {
        $iterator = new CachingIterator($this->getTraversable([]));

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    public function testPartialIterationDoesNotExhaust()
    {
        $traversable = $this->getTraversableThatThrows([1, 2, new Exception]);
        $iterator = new CachingIterator($traversable);

        $expectedKey = 0;
        $expectedItem = 1;

        foreach ($iterator as $key => $item) {
            $this->assertSame($expectedKey++, $key);
            $this->assertSame($expectedItem++, $item);

            if ($key === 1) {
                break;
            }
        }

        $this->assertTrue($iterator->valid());
    }

    public function testRewindAfterPartialIteration()
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame(0, $iterator->key());
        $this->assertSame(1, $iterator->current());

        $iterator->next();
        $this->assertSame([1, 2, 3], iterator_to_array($iterator));
    }

    public function testCount()
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));
        $this->assertCount(3, $iterator);
    }

    public function testCountAfterPartialIteration()
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame(0, $iterator->key());
        $this->assertSame(1, $iterator->current());

        $iterator->next();
        $this->assertCount(3, $iterator);
    }

    public function testCountWithEmptySet()
    {
        $iterator = new CachingIterator($this->getTraversable([]));
        $this->assertCount(0, $iterator);
    }

    private function getTraversable($items)
    {
        foreach ($items as $item) {
            yield $item;
        }
    }

    private function getTraversableThatThrows($items)
    {
        foreach ($items as $item) {
            if ($item instanceof Exception) {
                throw $item;
            } else {
                yield $item;
            }
        }
    }
}
