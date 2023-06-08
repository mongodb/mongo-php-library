<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Operation\BulkWrite;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;

class BulkWriteTest extends TestCase
{
    public function testOperationsMustNotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$operations is empty');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    public function testOperationsMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$operations is not a list');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            1 => [BulkWrite::INSERT_ONE => [['x' => 1]]],
        ]);
    }

    public function testMultipleOperationsInOneElement(): void
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

    public function testUnknownOperation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown operation type "foo" in $operations[0]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            ['foo' => [['_id' => 1]]],
        ]);
    }

    public function testInsertOneDocumentArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["insertOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::INSERT_ONE => []],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testInsertOneDocumentArgumentTypeCheck($document): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["insertOne"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::INSERT_ONE => [$document]],
        ]);
    }

    public function testInsertOneWithCodecRejectsInvalidDocuments(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue([]));

        new BulkWrite(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            [[BulkWrite::INSERT_ONE => [['x' => 1]]]],
            ['codec' => new TestDocumentCodec()],
        );
    }

    public function testDeleteManyFilterArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["deleteMany"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_MANY => []],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testDeleteManyFilterArgumentTypeCheck($document): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["deleteMany"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_MANY => [$document]],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testDeleteManyCollationOptionTypeCheck($collation): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["deleteMany"\]\[1\]\["collation"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_MANY => [['x' => 1], ['collation' => $collation]]],
        ]);
    }

    public function testDeleteOneFilterArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["deleteOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_ONE => []],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testDeleteOneFilterArgumentTypeCheck($document): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["deleteOne"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_ONE => [$document]],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testDeleteOneCollationOptionTypeCheck($collation): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["deleteOne"\]\[1\]\["collation"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_ONE => [['x' => 1], ['collation' => $collation]]],
        ]);
    }

    public function testReplaceOneFilterArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["replaceOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => []],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testReplaceOneFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [$filter, ['y' => 1]]],
        ]);
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @doesNotPerformAssertions
     */
    public function testReplaceOneReplacementArgument($replacement): void
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], $replacement]],
        ]);
    }

    public function testReplaceOneReplacementArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing second argument for $operations[0]["replaceOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1]]],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testReplaceOneReplacementArgumentTypeCheck($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[1\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], $replacement]],
        ]);
    }

    /** @dataProvider provideUpdateDocuments */
    public function testReplaceOneReplacementArgumentProhibitsUpdateDocument($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $operations[0]["replaceOne"][1] is an update operator');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], $replacement]],
        ]);
    }

    /**
     * @dataProvider provideUpdatePipelines
     * @dataProvider provideEmptyUpdatePipelinesExcludingArray
     */
    public function testReplaceOneReplacementArgumentProhibitsUpdatePipeline($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#(\$operations\[0\]\["replaceOne"\]\[1\] is an update pipeline)|(\$operations\[0\]\["replaceOne"\]\[1\] to have type "document" \(array or object\))#');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], $replacement]],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testReplaceOneCollationOptionTypeCheck($collation): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[2\]\["collation"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], ['y' => 1], ['collation' => $collation]]],
        ]);
    }

    /** @dataProvider provideInvalidBooleanValues */
    public function testReplaceOneUpsertOptionTypeCheck($upsert): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[2\]\["upsert"\] to have type "boolean" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], ['y' => 1], ['upsert' => $upsert]]],
        ]);
    }

    public function provideInvalidBooleanValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidBooleanValues());
    }

    public function testReplaceOneWithCodecRejectsInvalidDocuments(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue([]));

        new BulkWrite(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            [[BulkWrite::REPLACE_ONE => [['x' => 1], ['y' => 1]]]],
            ['codec' => new TestDocumentCodec()],
        );
    }

    public function testUpdateManyFilterArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["updateMany"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => []],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testUpdateManyFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateMany"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [$filter, ['$set' => ['x' => 1]]]],
        ]);
    }

    /**
     * @dataProvider provideUpdateDocuments
     * @dataProvider provideUpdatePipelines
     * @doesNotPerformAssertions
     */
    public function testUpdateManyUpdateArgument($update): void
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], $update]],
        ]);
    }

    public function testUpdateManyUpdateArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing second argument for $operations[0]["updateMany"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1]]],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testUpdateManyUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected update operator(s) or non-empty pipeline for $operations[0]["updateMany"][1]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], $update]],
        ]);
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @dataProvider provideEmptyUpdatePipelines
     */
    public function testUpdateManyUpdateArgumentProhibitsReplacementDocumentOrEmptyPipeline($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected update operator(s) or non-empty pipeline for $operations[0]["updateMany"][1]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], $update]],
        ]);
    }

    /** @dataProvider provideInvalidArrayValues */
    public function testUpdateManyArrayFiltersOptionTypeCheck($arrayFilters): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["arrayFilters"\] to have type "array" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['arrayFilters' => $arrayFilters]]],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testUpdateManyCollationOptionTypeCheck($collation): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["collation"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['collation' => $collation]]],
        ]);
    }

    /** @dataProvider provideInvalidBooleanValues */
    public function testUpdateManyUpsertOptionTypeCheck($upsert): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["upsert"\] to have type "boolean" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['upsert' => $upsert]]],
        ]);
    }

    /**
     * @dataProvider provideUpdateDocuments
     * @dataProvider provideUpdatePipelines
     * @doesNotPerformAssertions
     */
    public function testUpdateOneUpdateArgument($update): void
    {
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], $update]],
        ]);
    }

    public function testUpdateOneFilterArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing first argument for $operations[0]["updateOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => []],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testUpdateOneFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateOne"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [$filter, ['$set' => ['x' => 1]]]],
        ]);
    }

    public function testUpdateOneUpdateArgumentMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing second argument for $operations[0]["updateOne"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1]]],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testUpdateOneUpdateArgumentTypeCheck($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected update operator(s) or non-empty pipeline for $operations[0]["updateOne"][1]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], $update]],
        ]);
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @dataProvider provideEmptyUpdatePipelines
     */
    public function testUpdateOneUpdateArgumentProhibitsReplacementDocumentOrEmptyPipeline($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected update operator(s) or non-empty pipeline for $operations[0]["updateOne"][1]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], $update]],
        ]);
    }

    /** @dataProvider provideInvalidArrayValues */
    public function testUpdateOneArrayFiltersOptionTypeCheck($arrayFilters): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["arrayFilters"\] to have type "array" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['arrayFilters' => $arrayFilters]]],
        ]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testUpdateOneCollationOptionTypeCheck($collation): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["collation"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['collation' => $collation]]],
        ]);
    }

    /** @dataProvider provideInvalidBooleanValues */
    public function testUpdateOneUpsertOptionTypeCheck($upsert): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["upsert"\] to have type "boolean" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['upsert' => $upsert]]],
        ]);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new BulkWrite(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            [[BulkWrite::INSERT_ONE => [['x' => 1]]]],
            $options,
        );
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'bypassDocumentValidation' => $this->getInvalidBooleanValues(),
            'codec' => $this->getInvalidDocumentCodecValues(),
            'ordered' => $this->getInvalidBooleanValues(true),
            'session' => $this->getInvalidSessionValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
        ]);
    }
}
