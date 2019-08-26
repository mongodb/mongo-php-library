<?php

namespace MongoDB\Tests;

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Exception\InvalidArgumentException;

/**
 * Unit tests for utility functions.
 */
class FunctionsTest extends TestCase
{
    /**
     * @dataProvider provideDocumentAndTypeMap
     */
    public function testApplyTypeMapToDocument($document, array $typeMap, $expectedDocument)
    {
        $this->assertEquals($expectedDocument, \MongoDB\apply_type_map_to_document($document, $typeMap));
    }

    public function provideDocumentAndTypeMap()
    {
        return [
            [
                [
                    'x' => 1,
                    'y' => (object) ['foo' => 'bar'],
                    'z' => [1, 2, 3],
                ],
                [
                    'root' => 'object',
                    'document' => 'stdClass',
                    'array' => 'array',
                ],
                (object) [
                    'x' => 1,
                    'y' => (object) ['foo' => 'bar'],
                    'z' => [1, 2, 3],
                ],
            ],
            [
                [
                    'x' => 1,
                    'y' => (object) ['foo' => 'bar'],
                    'z' => [1, 2, 3],
                ],
                [
                    'root' => BSONDocument::class,
                    'document' => BSONDocument::class,
                    'array' => BSONArray::class,
                ],
                new BSONDocument([
                    'x' => 1,
                    'y' => new BSONDocument(['foo' => 'bar']),
                    'z' => new BSONArray([1, 2, 3]),
                ]),
            ],
            [
                [
                    'x' => 1,
                    'random' => [
                        'foo' => 'bar',
                    ],
                    'value' => [
                        'bar' => 'baz',
                        'embedded' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
                [
                    'root' => 'array',
                    'document' => 'stdClass',
                    'array' => 'array',
                    'fieldPaths' => [
                        'value' => 'array',
                    ],
                ],
                [
                    'x' => 1,
                    'random' => (object) [
                        'foo' => 'bar',
                    ],
                    'value' => [
                        'bar' => 'baz',
                        'embedded' => (object) [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * @dataProvider provideIndexSpecificationDocumentsAndGeneratedNames
     */
    public function testGenerateIndexName($document, $expectedName)
    {
        $this->assertSame($expectedName, \MongoDB\generate_index_name($document));
    }

    public function provideIndexSpecificationDocumentsAndGeneratedNames()
    {
        return [
            [ ['x' => 1], 'x_1' ],
            [ ['x' => -1, 'y' => 1], 'x_-1_y_1' ],
            [ ['x' => '2dsphere', 'y' => 1 ], 'x_2dsphere_y_1' ],
            [ (object) ['x' => 1], 'x_1' ],
            [ new BSONDocument(['x' => 1]), 'x_1' ],
        ];
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testGenerateIndexNameArgumentTypeCheck($document)
    {
        $this->expectException(InvalidArgumentException::class);
        \MongoDB\generate_index_name($document);
    }

    /**
     * @dataProvider provideIsFirstKeyOperatorDocuments
     */
    public function testIsFirstKeyOperator($document, $isFirstKeyOperator)
    {
        $this->assertSame($isFirstKeyOperator, \MongoDB\is_first_key_operator($document));
    }

    public function provideIsFirstKeyOperatorDocuments()
    {
        return [
            [ ['y' => 1], false ],
            [ (object) ['y' => 1], false ],
            [ new BSONDocument(['y' => 1]), false ],
            [ ['$set' => ['y' => 1]], true ],
            [ (object) ['$set' => ['y' => 1]], true ],
            [ new BSONDocument(['$set' => ['y' => 1]]), true ],
        ];
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testIsFirstKeyOperatorArgumentTypeCheck($document)
    {
        $this->expectException(InvalidArgumentException::class);
        \MongoDB\is_first_key_operator($document);
    }

    /**
     * @dataProvider provideMapReduceOutValues
     */
    public function testIsMapReduceOutputInline($out, $isInline)
    {
        $this->assertSame($isInline, \MongoDB\is_mapreduce_output_inline($out));
    }

    public function provideMapReduceOutValues()
    {
        return [
            [ 'collectionName', false ],
            [ ['inline' => 1], true ],
            [ ['inline' => 0], true ], // only the key is significant
            [ ['replace' => 'collectionName'], false ],
        ];
    }

    /**
     * @dataProvider provideTypeMapValues
     */
    public function testCreateFieldPathTypeMap(array $expected, array $typeMap, $fieldPath = 'field')
    {
        $this->assertEquals($expected, \MongoDB\create_field_path_type_map($typeMap, $fieldPath));
    }

    public function provideTypeMapValues()
    {
        return [
            'No root type' => [
                ['document' => 'array', 'root' => 'object'],
                ['document' => 'array'],
            ],
            'No field path' => [
                ['root' => 'object', 'fieldPaths' => ['field' => 'array']],
                ['root' => 'array'],
            ],
            'Field path exists' => [
                ['root' => 'object', 'fieldPaths' => ['field' => 'array', 'field.field' => 'object']],
                ['root' => 'array', 'fieldPaths' => ['field' => 'object']],
            ],
            'Nested field path' => [
                ['root' => 'object', 'fieldPaths' => ['field' => 'object', 'field.nested' => 'array']],
                ['root' => 'object', 'fieldPaths' => ['nested' => 'array']],
            ],
            'Array field path converted to array' => [
                [
                    'root' => 'object',
                    'array' => 'MongoDB\Model\BSONArray',
                    'fieldPaths' => [
                        'field' => 'array',
                        'field.$' => 'object',
                        'field.$.nested' => 'array',
                    ]
                ],
                [
                    'root' => 'object',
                    'array' => 'MongoDB\Model\BSONArray',
                    'fieldPaths' => [
                        'nested' => 'array',
                    ]
                ],
                'field.$',
            ],
            'Array field path without root key' => [
                [
                    'root' => 'object',
                    'array' => 'MongoDB\Model\BSONArray',
                    'fieldPaths' => [
                        'field' => 'array',
                        'field.$.nested' => 'array',
                    ]
                ],
                [
                    'array' => 'MongoDB\Model\BSONArray',
                    'fieldPaths' => [
                        'nested' => 'array',
                    ]
                ],
                'field.$',
            ],
        ];
    }
}
