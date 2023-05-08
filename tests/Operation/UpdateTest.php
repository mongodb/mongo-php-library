<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\Update;

class UpdateTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$filter to have type "array or object" but found "[\w ]+"/');
        new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, ['$set' => ['x' => 1]]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$update to have type "array or object" but found "[\w ]+"/');
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['y' => 1], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['arrayFilters' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['bypassDocumentValidation' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['collation' => $value];
        }

        foreach ($this->getInvalidBooleanValues(true) as $value) {
            $options[][] = ['multi' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidBooleanValues(true) as $value) {
            $options[][] = ['upsert' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }

    /** @dataProvider provideInvalidUpdateValues */
    public function testConstructorMultiOptionRequiresOperators($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"multi" option cannot be true if $update is a replacement document');
        new Update($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $update, ['multi' => true]);
    }

    public function provideInvalidUpdateValues(): array
    {
        return [
            'replacement:array' => [['x' => 1]],
            'replacement:object' => [(object) ['x' => 1]],
            'replacement:Serializable' => [new BSONDocument(['x' => 1])],
            'replacement:Document' => [Document::fromPHP(['x' => 1])],
            'empty_pipeline:array' => [[]],
            'empty_pipeline:Serializable' => [new BSONArray([])],
            'empty_pipeline:PackedArray' => [PackedArray::fromPHP([])],
        ];
    }
}
