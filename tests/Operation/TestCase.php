<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\TestCase as BaseTestCase;

/**
 * Base class for Operation unit tests.
 */
abstract class TestCase extends BaseTestCase
{
    public function provideReplacementDocuments(): array
    {
        return [
            'replacement:array' => [['x' => 1]],
            'replacement:object' => [(object) ['x' => 1]],
            'replacement:Serializable' => [new BSONDocument(['x' => 1])],
            'replacement:Document' => [Document::fromPHP(['x' => 1])],
            /* Note: empty arrays could also express a pipeline, but PHPLIB
             * interprets them as a replacement document for BC. */
            'empty_replacement:array' => [[]],
            'empty_replacement:object' => [(object) []],
            'empty_replacement:Serializable' => [new BSONDocument([])],
            'empty_replacement:Document' => [Document::fromPHP([])],
        ];
    }

    public function provideUpdateDocuments(): array
    {
        return [
            'update:array' => [['$set' => ['x' => 1]]],
            'update:object' => [(object) ['$set' => ['x' => 1]]],
            'update:Serializable' => [new BSONDocument(['$set' => ['x' => 1]])],
            'update:Document' => [Document::fromPHP(['$set' => ['x' => 1]])],
        ];
    }

    public function provideUpdatePipelines(): array
    {
        return [
            'pipeline:array' => [[['$set' => ['x' => 1]]]],
            'pipeline:Serializable' => [new BSONArray([['$set' => ['x' => 1]]])],
            'pipeline:PackedArray' => [PackedArray::fromPHP([['$set' => ['x' => 1]]])],
        ];
    }

    public function provideEmptyUpdatePipelines(): array
    {
        /* Empty update pipelines are accepted by the update and findAndModify
         * commands (as NOPs); however, they are not supported for updates in
         * libmongoc because empty arrays and documents have the same bson_t
         * representation (libmongoc considers it an empty replacement for BC).
         * For consistency, PHPLIB rejects empty pipelines for updateOne,
         * updateMany, and findOneAndUpdate operations. Replace operations
         * interpret empty arrays as replacement documents for BC, but rejects
         * other representations. */
        return [
            'empty_pipeline:array' => [[]],
            'empty_pipeline:Serializable' => [new BSONArray([])],
            'empty_pipeline:PackedArray' => [PackedArray::fromPHP([])],
        ];
    }

    public function provideEmptyUpdatePipelinesExcludingArray(): array
    {
        /* This data provider is used for replace operations, which accept empty
         * arrays as replacement documents for BC. */
        return [
            'empty_pipeline:Serializable' => [new BSONArray([])],
            'empty_pipeline:PackedArray' => [PackedArray::fromPHP([])],
        ];
    }

    public function provideInvalidUpdateValues(): array
    {
        return $this->wrapValuesForDataProvider($this->getInvalidUpdateValues());
    }

    protected function getInvalidUpdateValues(): array
    {
        return [123, 3.14, 'foo', true];
    }
}
