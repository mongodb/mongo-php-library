<?php

namespace MongoDB\Tests;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Stage\LimitStage;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Driver\WriteConcern;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

use function MongoDB\apply_type_map_to_document;
use function MongoDB\create_field_path_type_map;
use function MongoDB\document_to_array;
use function MongoDB\is_builder_pipeline;
use function MongoDB\is_first_key_operator;
use function MongoDB\is_last_pipeline_operator_write;
use function MongoDB\is_pipeline;
use function MongoDB\is_write_concern_acknowledged;

/**
 * Unit tests for utility functions.
 */
class FunctionsTest extends TestCase
{
    #[DataProvider('provideDocumentAndTypeMap')]
    public function testApplyTypeMapToDocument($document, array $typeMap, $expectedDocument): void
    {
        $this->assertEquals($expectedDocument, apply_type_map_to_document($document, $typeMap));
    }

    public static function provideDocumentAndTypeMap()
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

    #[DataProvider('provideDocumentsAndExpectedArrays')]
    public function testDocumentToArray($document, array $expectedArray): void
    {
        $this->assertSame($expectedArray, document_to_array($document));
    }

    public static function provideDocumentsAndExpectedArrays(): array
    {
        return [
            'array' => [['x' => 1], ['x' => 1]],
            'object' => [(object) ['x' => 1], ['x' => 1]],
            'Serializable' => [new BSONDocument(['x' => 1]), ['x' => 1]],
            'Document' => [Document::fromPHP(['x' => 1]), ['x' => 1]],
            // PackedArray and array-returning Serializable are both allowed
            'PackedArray' => [PackedArray::fromPHP(['foo']), [0 => 'foo']],
            'Serializable:array' => [new BSONArray(['foo']), [0 => 'foo']],
        ];
    }

    #[DataProvider('provideInvalidDocumentValuesForChecks')]
    public function testDocumentToArrayArgumentTypeCheck($document): void
    {
        $this->expectException(TypeError::class);
        document_to_array($document);
    }

    public static function provideInvalidDocumentValuesForChecks(): array
    {
        // PackedArray is intentionally left out, as document_to_array is used to convert aggregation pipelines
        return self::wrapValuesForDataProvider([123, 3.14, 'foo', true]);
    }

    public static function provideDocumentCasts(): array
    {
        // phpcs:disable SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing
        // phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration
        // phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
        return [
            'array' => [fn ($value) => $value],
            'object' => [fn ($value) => (object) $value],
            'Serializable' => [fn ($value) => new BSONDocument($value)],
            'Document' => [fn ($value) => Document::fromPHP($value)],
        ];
        // phpcs:enable
    }

    #[DataProvider('provideDocumentCasts')]
    public function testIsFirstKeyOperator(callable $cast): void
    {
        $this->assertFalse(is_first_key_operator($cast(['y' => 1])));
        $this->assertTrue(is_first_key_operator($cast(['$set' => ['y' => 1]])));

        // Empty and packed arrays are unlikely arguments, but still valid
        $this->assertFalse(is_first_key_operator($cast([])));
        $this->assertFalse(is_first_key_operator($cast(['foo'])));
    }

    #[DataProvider('provideInvalidDocumentValuesForChecks')]
    public function testIsFirstKeyOperatorArgumentTypeCheck($document): void
    {
        $this->expectException(TypeError::class);
        is_first_key_operator($document);
    }

    #[DataProvider('provideTypeMapValues')]
    public function testCreateFieldPathTypeMap(array $expected, array $typeMap, $fieldPath = 'field'): void
    {
        $this->assertEquals($expected, create_field_path_type_map($typeMap, $fieldPath));
    }

    public static function provideTypeMapValues()
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
                    'array' => BSONArray::class,
                    'fieldPaths' => [
                        'field' => 'array',
                        'field.$' => 'object',
                        'field.$.nested' => 'array',
                    ],
                ],
                [
                    'root' => 'object',
                    'array' => BSONArray::class,
                    'fieldPaths' => ['nested' => 'array'],
                ],
                'field.$',
            ],
            'Array field path without root key' => [
                [
                    'root' => 'object',
                    'array' => BSONArray::class,
                    'fieldPaths' => [
                        'field' => 'array',
                        'field.$.nested' => 'array',
                    ],
                ],
                [
                    'array' => BSONArray::class,
                    'fieldPaths' => ['nested' => 'array'],
                ],
                'field.$',
            ],
        ];
    }

    #[DataProvider('provideDocumentCasts')]
    public function testIsLastPipelineOperatorWrite(callable $cast): void
    {
        $match = ['$match' => ['x' => 1]];
        $merge = ['$merge' => ['into' => 'coll']];
        $out = ['$out' => ['db' => 'db', 'coll' => 'coll']];

        $this->assertTrue(is_last_pipeline_operator_write([$cast($merge)]));
        $this->assertTrue(is_last_pipeline_operator_write([$cast($out)]));
        $this->assertTrue(is_last_pipeline_operator_write([$cast($match), $cast($merge)]));
        $this->assertTrue(is_last_pipeline_operator_write([$cast($match), $cast($out)]));
        $this->assertFalse(is_last_pipeline_operator_write([$cast($match)]));
        $this->assertFalse(is_last_pipeline_operator_write([$cast($merge), $cast($match)]));
        $this->assertFalse(is_last_pipeline_operator_write([$cast($out), $cast($match)]));
    }

    #[DataProvider('providePipelines')]
    public function testIsPipeline($expected, $pipeline, $allowEmpty = false): void
    {
        $this->assertSame($expected, is_pipeline($pipeline, $allowEmpty));
    }

    public static function providePipelines(): array
    {
        $valid = [
            ['$match' => ['foo' => 'bar']],
            (object) ['$group' => ['_id' => 1]],
            new BSONDocument(['$skip' => 1]),
            Document::fromPHP(['$limit' => 1]),
        ];

        $invalidIndex = [1 => ['$group' => ['_id' => 1]]];

        $invalidStageKey = [['group' => ['_id' => 1]]];

        $dbrefInNumericField = ['0' => ['$ref' => 'foo', '$id' => 'bar']];

        return [
            // Valid pipeline in various forms
            'valid: array' => [true, $valid],
            'valid: Serializable' => [true, new BSONArray($valid)],
            'valid: PackedArray' => [true, PackedArray::fromPHP($valid)],
            // Invalid type for an otherwise valid pipeline
            'invalid type: stdClass' => [false, (object) $valid],
            'invalid type: Serializable' => [false, new BSONDocument($valid)],
            'invalid type: Document' => [false, Document::fromPHP($valid)],
            // Invalid index in pipeline array
            'invalid index: array' => [false, $invalidIndex],
            // Note: PackedArray::fromPHP() requires a list array
            // Note: BSONArray::bsonSerialize() re-indexes the array
            'invalid index: array' => [true, new BSONArray($invalidIndex)],
            // Invalid stage key in pipeline element
            'invalid stage key: array' => [false, $invalidStageKey],
            'invalid stage key: Serializable' => [false, new BSONArray($invalidStageKey)],
            'invalid stage key: PackedArray' => [false, PackedArray::fromPHP($invalidStageKey)],
            // Invalid pipeline element type
            'invalid pipeline element type: array' => [false, [[[]]]],
            'invalid pipeline element type: Serializable' => [false, new BSONArray([new BSONArray([])])],
            'invalid pipeline element type: PackedArray' => [false, PackedArray::fromPHP([[]])],
            // Empty array has no pipeline stages
            'valid empty: array' => [true, [], true],
            'valid empty: Serializable' => [true, new BSONArray([]), true],
            'valid empty: PackedArray' => [true, PackedArray::fromPHP([]), true],
            'invalid empty: array' => [false, []],
            'invalid empty: Serializable' => [false, new BSONArray([])],
            'invalid empty: PackedArray' => [false, PackedArray::fromPHP([])],
            // False positive: DBRef in numeric field
            'false positive DBRef: array' => [true, $dbrefInNumericField],
            'false positive DBRef: Serializable' => [true, new BSONArray($dbrefInNumericField)],
            'false positive DBRef: PackedArray' => [true, PackedArray::fromPHP($dbrefInNumericField)],
            // Invalid document containing DBRef in numeric field
            'invalid DBRef: stdClass' => [false, (object) $dbrefInNumericField],
            'invalid DBRef: Serializable' => [false, new BSONDocument($dbrefInNumericField)],
            'invalid DBRef: Document' => [false, Document::fromPHP($dbrefInNumericField)],
            // Additional invalid cases
            'Update document' => [false, ['$set' => ['foo' => 'bar']]],
            'Replacement document with DBRef' => [false, ['x' => ['$ref' => 'foo', '$id' => 'bar']]],
        ];
    }

    #[DataProvider('provideStagePipelines')]
    public function testIsBuilderPipeline($expected, $pipeline): void
    {
        $this->assertSame($expected, is_builder_pipeline($pipeline));
    }

    public static function provideStagePipelines(): iterable
    {
        yield 'empty array' => [false, []];
        yield 'array of arrays' => [false, [['$match' => ['x' => 1]]]];
        yield 'map of stages' => [false, [1 => new MatchStage([])]];
        yield 'stages' => [true, [new MatchStage([]), new LimitStage(1)]];
        yield 'stages and operators' => [true, [new MatchStage([]), ['$limit' => 1]]];
    }

    #[DataProvider('provideWriteConcerns')]
    public function testIsWriteConcernAcknowledged($expected, WriteConcern $writeConcern): void
    {
        $this->assertSame($expected, is_write_concern_acknowledged($writeConcern));
    }

    public static function provideWriteConcerns(): array
    {
        // Note: WriteConcern constructor prohibits w=-1 or w=0 and journal=true
        return [
            'MONGOC_WRITE_CONCERN_W_MAJORITY' => [true, new WriteConcern(-3)],
            'MONGOC_WRITE_CONCERN_W_DEFAULT' => [true, new WriteConcern(-2)],
            'MONGOC_WRITE_CONCERN_W_DEFAULT and journal=true' => [true, new WriteConcern(-2, 0, true)],
            'MONGOC_WRITE_CONCERN_W_ERRORS_IGNORED' => [false, new WriteConcern(-1)],
            'MONGOC_WRITE_CONCERN_W_ERRORS_IGNORED and journal=false' => [false, new WriteConcern(-1, 0, false)],
            'MONGOC_WRITE_CONCERN_W_UNACKNOWLEDGED' => [false, new WriteConcern(0)],
            'MONGOC_WRITE_CONCERN_W_UNACKNOWLEDGED and journal=false' => [false, new WriteConcern(0, 0, false)],
            'w=1' => [true, new WriteConcern(1)],
            'w=1 and journal=false' => [true, new WriteConcern(1, 0, false)],
            'w=1 and journal=true' => [true, new WriteConcern(1, 0, true)],
            'majority' => [true, new WriteConcern(WriteConcern::MAJORITY)],
            'tag' => [true, new WriteConcern('tag')],
        ];
    }
}
