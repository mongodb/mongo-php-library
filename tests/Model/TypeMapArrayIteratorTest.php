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
     * @dataProvider provideMutateMethods
     */
    public function testMutateMethodsCannotBeCalled($method, $args)
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

        $iterator->rewind();

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('MongoDB\Model\TypeMapArrayIterator is immutable');
        call_user_func_array([$iterator, $method], $args);
    }

    public function provideMutateMethods()
    {
        return [
            ['append', [['x' => 1]]],
            ['asort', []],
            ['ksort', []],
            ['natcasesort', []],
            ['natsort', []],
            ['offsetSet', [0, ['x' => 1]]],
            ['offsetUnset', [0]],
            ['uasort', [function($a, $b) { return 0; }]],
            ['uksort', [function($a, $b) { return 0; }]],
        ];
    }
}
