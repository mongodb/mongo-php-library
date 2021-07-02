<?php

namespace MongoDB\Tests\Model;

use Exception;
use Iterator;
use MongoDB\Model\CachingIterator;
use MongoDB\Tests\TestCase;
use Throwable;

use function iterator_to_array;

class CachingIteratorTest extends TestCase
{
    public function testTraversingGeneratorConsumesIt(): void
    {
        $iterator = $this->getTraversable([1, 2, 3]);
        $this->assertSame([1, 2, 3], iterator_to_array($iterator));

        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Cannot traverse an already closed generator');
        iterator_to_array($iterator);
    }

    public function testConstructorRewinds(): void
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));

        $this->assertTrue($iterator->valid());
        $this->assertSame(0, $iterator->key());
        $this->assertSame(1, $iterator->current());
    }

    public function testIteration(): void
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

    public function testIterationWithEmptySet(): void
    {
        $iterator = new CachingIterator($this->getTraversable([]));

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    public function testPartialIterationDoesNotExhaust(): void
    {
        $traversable = $this->getTraversable([1, 2, new Exception()]);
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

    public function testRewindAfterPartialIteration(): void
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame(0, $iterator->key());
        $this->assertSame(1, $iterator->current());

        $iterator->next();
        $this->assertSame([1, 2, 3], iterator_to_array($iterator));
    }

    public function testCount(): void
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));
        $this->assertCount(3, $iterator);
    }

    public function testCountAfterPartialIteration(): void
    {
        $iterator = new CachingIterator($this->getTraversable([1, 2, 3]));

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame(0, $iterator->key());
        $this->assertSame(1, $iterator->current());

        $iterator->next();
        $this->assertCount(3, $iterator);
    }

    public function testCountWithEmptySet(): void
    {
        $iterator = new CachingIterator($this->getTraversable([]));
        $this->assertCount(0, $iterator);
    }

    /**
     * This protects against iterators that return valid keys on invalid
     * positions, which was the case in ext-mongodb until PHPC-1748 was fixed.
     */
    public function testWithWrongIterator(): void
    {
        $nestedIterator = new class implements Iterator {
            /** @var int */
            private $i = 0;

            public function current()
            {
                return $this->i;
            }

            public function next(): void
            {
                $this->i++;
            }

            public function key()
            {
                return $this->i;
            }

            public function valid()
            {
                return $this->i == 0;
            }

            public function rewind(): void
            {
                $this->i = 0;
            }
        };

        $iterator = new CachingIterator($nestedIterator);
        $this->assertCount(1, $iterator);
    }

    private function getTraversable($items)
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
