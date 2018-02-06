<?php

namespace MongoDB\Tests;

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\WriteConcern;

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
                    'root' => 'MongoDB\Model\BSONDocument',
                    'document' => 'MongoDB\Model\BSONDocument',
                    'array' => 'MongoDB\Model\BSONArray',
                ],
                new BSONDocument([
                    'x' => 1,
                    'y' => new BSONDocument(['foo' => 'bar']),
                    'z' => new BSONArray([1, 2, 3]),
                ]),
            ],
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
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testGenerateIndexNameArgumentTypeCheck($document)
    {
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
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidDocumentValues
     */
    public function testIsFirstKeyOperatorArgumentTypeCheck($document)
    {
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
}
