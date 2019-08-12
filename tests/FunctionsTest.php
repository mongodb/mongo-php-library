<?php

namespace MongoDB\Tests;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use function MongoDB\apply_type_map_to_document;
use function MongoDB\create_field_path_type_map;
use function MongoDB\generate_index_name;
use function MongoDB\is_first_key_operator;
use function MongoDB\is_mapreduce_output_inline;
use function MongoDB\is_pipeline;

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
        $this->assertEquals($expectedDocument, apply_type_map_to_document($document, $typeMap));
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
                    'random' => ['foo' => 'bar'],
                    'value' => [
                        'bar' => 'baz',
                        'embedded' => ['foo' => 'bar'],
                    ],
                ],
                [
                    'root' => 'array',
                    'document' => 'stdClass',
                    'array' => 'array',
                    'fieldPaths' => ['value' => 'array'],
                ],
                [
                    'x' => 1,
                    'random' => (object) ['foo' => 'bar'],
                    'value' => [
                        'bar' => 'baz',
                        'embedded' => (object) ['foo' => 'bar'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideIndexSpecificationDocumentsAndGeneratedNames
     */
    public function testGenerateIndexName($document, $expectedName)
    {
        $this->assertSame($expectedName, generate_index_name($document));
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
        generate_index_name($document);
    }

    /**
     * @dataProvider provideIsFirstKeyOperatorDocuments
     */
    public function testIsFirstKeyOperator($document, $isFirstKeyOperator)
    {
        $this->assertSame($isFirstKeyOperator, is_first_key_operator($document));
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
        is_first_key_operator($document);
    }

    /**
     * @dataProvider provideMapReduceOutValues
     */
    public function testIsMapReduceOutputInline($out, $isInline)
    {
        $this->assertSame($isInline, is_mapreduce_output_inline($out));
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
        $this->assertEquals($expected, create_field_path_type_map($typeMap, $fieldPath));
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
                    ],
                ],
                [
                    'root' => 'object',
                    'array' => 'MongoDB\Model\BSONArray',
                    'fieldPaths' => ['nested' => 'array'],
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
                    ],
                ],
                [
                    'array' => 'MongoDB\Model\BSONArray',
                    'fieldPaths' => ['nested' => 'array'],
                ],
                'field.$',
            ],
        ];
    }

    /**
     * @dataProvider providePipelines
     */
    public function testIsPipeline($expected, $pipeline)
    {
        $this->assertSame($expected, is_pipeline($pipeline));
    }

    public function providePipelines()
    {
        return [
            'Not an array' => [false, (object) []],
            'Empty array' => [false, []],
            'Non-sequential indexes in array' => [false, [1 => ['$group' => []]]],
            'Update document instead of pipeline' => [false, ['$set' => ['foo' => 'bar']]],
            'Invalid pipeline stage' => [false, [['group' => []]]],
            'Update with DbRef' => [false, ['x' => ['$ref' => 'foo', '$id' => 'bar']]],
            'Valid pipeline' => [
                true,
                [
                    ['$match' => ['foo' => 'bar']],
                    ['$group' => ['_id' => 1]],
                ],
            ],
            'False positive with DbRef in numeric field' => [true, ['0' => ['$ref' => 'foo', '$id' => 'bar']]],
            'DbRef in numeric field as object' => [false, (object) ['0' => ['$ref' => 'foo', '$id' => 'bar']]],
        ];
    }
}
