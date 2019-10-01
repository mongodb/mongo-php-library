<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\BulkWrite;

class BulkWriteTest extends TestCase
{
    public function testOperationsMustNotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$operations is empty');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    public function testOperationsMustBeAList()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$operations is not a list (unexpected index: "1")');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            1 => [BulkWrite::INSERT_ONE => [['x' => 1]]],
        ]);
    }

    public function testMultipleOperationsInOneElement()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected one element in $operation[0], actually: 2');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [
                BulkWrite::INSERT_ONE => [['x' => 1]],
                BulkWrite::DELETE_ONE => [['x' => 1]],
            ],
        ]);
    }

    public function testUnknownOperation()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown operation type "foo" in $operations[0]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            ['foo' => [['_id' => 1]]],
        ]);
    }

    public function testInsertOneDocumentArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["insertOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::INSERT_ONE => []],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testInsertOneDocumentArgumentTypeCheck($document)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["insertOne"\]\[0\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::INSERT_ONE => [$document]],
        ]);
    }

    public function testDeleteManyFilterArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["deleteMany"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_MANY => []],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testDeleteManyFilterArgumentTypeCheck($document)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["deleteMany"\]\[0\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_MANY => [$document]],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testDeleteManyCollationOptionTypeCheck($collation)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["deleteMany"\]\[1\]\["collation"\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_MANY => [['x' => 1], ['collation' => $collation]]],
        ]);
    }

    public function provideInvalidDocumentValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidDocumentValues());
    }

    public function testDeleteOneFilterArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["deleteOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_ONE => []],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testDeleteOneFilterArgumentTypeCheck($document)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["deleteOne"\]\[0\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_ONE => [$document]],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testDeleteOneCollationOptionTypeCheck($collation)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["deleteOne"\]\[1\]\["collation"\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_ONE => [['x' => 1], ['collation' => $collation]]],
        ]);
    }

    public function testReplaceOneFilterArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["replaceOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => []],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testReplaceOneFilterArgumentTypeCheck($filter)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["replaceOne"\]\[0\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [$filter, ['y' => 1]]],
        ]);
    }

    public function testReplaceOneReplacementArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing second argument for $operations[0]["replaceOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testReplaceOneReplacementArgumentTypeCheck($replacement)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["replaceOne"\]\[1\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], $replacement]],
        ]);
    }

    public function testReplaceOneReplacementArgumentRequiresNoOperators()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $operations[0]["replaceOne"][1] is an update operator');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['_id' => 1], ['$inc' => ['x' => 1]]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testReplaceOneCollationOptionTypeCheck($collation)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["replaceOne"\]\[2\]\["collation"\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], ['y' => 1], ['collation' => $collation]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidBooleanValues
     */
    public function testReplaceOneUpsertOptionTypeCheck($upsert)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["replaceOne"\]\[2\]\["upsert"\] to have type "boolean" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], ['y' => 1], ['upsert' => $upsert]]],
        ]);
    }

    public function provideInvalidBooleanValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidBooleanValues());
    }

    public function testUpdateManyFilterArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["updateMany"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => []],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateManyFilterArgumentTypeCheck($filter)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateMany"\]\[0\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [$filter, ['$set' => ['x' => 1]]]],
        ]);
    }

    public function testUpdateManyUpdateArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing second argument for $operations[0]["updateMany"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateManyUpdateArgumentTypeCheck($update)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateMany"\]\[1\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], $update]],
        ]);
    }

    public function testUpdateManyUpdateArgumentRequiresOperatorsOrPipeline()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $operations[0]["updateMany"][1] is neither an update operator nor a pipeline');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['_id' => ['$gt' => 1]], ['x' => 1]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidArrayValues
     */
    public function testUpdateManyArrayFiltersOptionTypeCheck($arrayFilters)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["arrayFilters"\] to have type "array" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['arrayFilters' => $arrayFilters]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateManyCollationOptionTypeCheck($collation)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["collation"\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['collation' => $collation]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidBooleanValues
     */
    public function testUpdateManyUpsertOptionTypeCheck($upsert)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["upsert"\] to have type "boolean" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['upsert' => $upsert]]],
        ]);
    }

    public function testUpdateOneFilterArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["updateOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => []],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateOneFilterArgumentTypeCheck($filter)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateOne"\]\[0\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [$filter, ['$set' => ['x' => 1]]]],
        ]);
    }

    public function testUpdateOneUpdateArgumentMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing second argument for $operations[0]["updateOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateOneUpdateArgumentTypeCheck($update)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateOne"\]\[1\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], $update]],
        ]);
    }

    public function testUpdateOneUpdateArgumentRequiresOperatorsOrPipeline()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $operations[0]["updateOne"][1] is neither an update operator nor a pipeline');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['_id' => 1], ['x' => 1]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidArrayValues
     */
    public function testUpdateOneArrayFiltersOptionTypeCheck($arrayFilters)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["arrayFilters"\] to have type "array" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['arrayFilters' => $arrayFilters]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testUpdateOneCollationOptionTypeCheck($collation)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["collation"\] to have type "array or object" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['collation' => $collation]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidBooleanValues
     */
    public function testUpdateOneUpsertOptionTypeCheck($upsert)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["upsert"\] to have type "boolean" but found "[\w ]+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['upsert' => $upsert]]],
        ]);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new BulkWrite(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            [[BulkWrite::INSERT_ONE => [['x' => 1]]]],
            $options
        );
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['bypassDocumentValidation' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['ordered' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }
}
