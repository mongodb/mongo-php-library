<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\WriteConcern;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;

/**
 * Base class for Operation functional tests.
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
    }

    public function provideFilterDocuments(): array
    {
        $expected = (object) ['x' => 1];

        return [
            'filter:array' => [['x' => 1], $expected],
            'filter:object' => [(object) ['x' => 1], $expected],
            'filter:Serializable' => [new BSONDocument(['x' => 1]), $expected],
            'filter:Document' => [Document::fromPHP(['x' => 1]), $expected],
        ];
    }

    public function provideReplacementDocuments(): array
    {
        $expected = (object) ['x' => 1];
        $expectedEmpty = (object) [];

        return [
            'replacement:array' => [['x' => 1], $expected],
            'replacement:object' => [(object) ['x' => 1], $expected],
            'replacement:Serializable' => [new BSONDocument(['x' => 1]), $expected],
            'replacement:Document' => [Document::fromPHP(['x' => 1]), $expected],
            /* Note: empty arrays could also express an empty pipeline, but we
             * should interpret them as an empty replacement document for BC. */
            'empty_replacement:array' => [[], $expectedEmpty],
            'empty_replacement:object' => [(object) [], $expectedEmpty],
            'empty_replacement:Serializable' => [new BSONDocument([]), $expectedEmpty],
            'empty_replacement:Document' => [Document::fromPHP([]), $expectedEmpty],
        ];
    }

    public function provideUpdateDocuments(): array
    {
        $expected = (object) ['$set' => (object) ['x' => 1]];

        return [
            'update:array' => [['$set' => ['x' => 1]], $expected],
            'update:object' => [(object) ['$set' => ['x' => 1]], $expected],
            'update:Serializable' => [new BSONDocument(['$set' => ['x' => 1]]), $expected],
            'update:Document' => [Document::fromPHP(['$set' => ['x' => 1]]), $expected],
        ];
    }

    public function provideUpdatePipelines(): array
    {
        $expected = [(object) ['$set' => (object) ['x' => 1]]];

        return [
            'pipeline:array' => [[['$set' => ['x' => 1]]], $expected],
            'pipeline:Serializable' => [new BSONArray([['$set' => ['x' => 1]]]), $expected],
            'pipeline:PackedArray' => [PackedArray::fromPHP([['$set' => ['x' => 1]]]), $expected],
            /* Note: although empty pipelines are valid NOPs for update and
             * findAndModify commands, they are not supported for updates in
             * libmongoc since they are indistinguishable from empty replacement
             * documents (both are empty bson_t structs). */
        ];
    }

    protected function createDefaultReadConcern()
    {
        return new ReadConcern();
    }

    protected function createDefaultWriteConcern()
    {
        return new WriteConcern(-2);
    }

    protected function createSession()
    {
        return $this->manager->startSession();
    }
}
