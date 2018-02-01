<?php

namespace MongoDB\Tests\Model;

use MongoDB\Exception\BadMethodCallException;
use MongoDB\Model\TypeMapArrayIterator;
use MongoDB\Tests\TestCase;

class TypeMapArrayIteratorTest extends TestCase
{
    public function testCurrentAppliesTypeMap()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $this->assertEquals($expectedDocument, $iterator->current());
    }

    public function testOffsetGetAppliesTypeMap()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $this->assertEquals($expectedDocument, $iterator->offsetGet(0));
    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testAppendThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $iterator->asort();

    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testAsortThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $iterator->asort();
    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testKsortThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $iterator->ksort();
    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testNatcasessortThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $iterator->natcasesort();
    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testNatsortThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $iterator->natsort();
    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testOffsetSetThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $iterator->offsetSet(0, 3);
    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testOffsetUnsetThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();

        $iterator->offsetUnset(0);
    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testUasortThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();
        $cmp_function = function($a, $b) {
            return $a;
        };
        $iterator->uasort($cmp_function(0, 1));
    }

    /**
     * @expectedException MongoDB\Exception\BadMethodCallException
     * @expectedExceptionMessage MongoDB\Model\TypeMapArrayIterator is immutable
     */
    public function testUksortThrowsException()
    {
        $document = [
            'array' => [1, 2, 3],
            'object' => ['foo' => 'bar'],
        ];

        $typeMap = [
            'root' => 'object',
            'document' => 'object',
            'array' => 'array',
        ];

        $iterator = new TypeMapArrayIterator([$document], $typeMap);

        $expectedDocument = (object) [
            'array' => [1, 2, 3],
            'object' => (object) ['foo' => 'bar'],
        ];

        $iterator->rewind();
        $cmp_function = function($a, $b) {
            return $a;
        };
        $iterator->uksort($cmp_function(0, 1));
    }
}
