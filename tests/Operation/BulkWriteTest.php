<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Operation\BulkWrite;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use TypeError;

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

    #[DataProvider('provideInvalidDocumentValues')]
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

    #[DataProvider('provideInvalidDocumentValues')]
    public function testDeleteManyFilterArgumentTypeCheck($document): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["deleteMany"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_MANY => [$document]],
        ]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
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

    #[DataProvider('provideInvalidDocumentValues')]
    public function testDeleteOneFilterArgumentTypeCheck($document): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["deleteOne"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::DELETE_ONE => [$document]],
        ]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
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

    #[DataProvider('provideInvalidDocumentValues')]
    public function testReplaceOneFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [$filter, ['y' => 1]]],
        ]);
    }

    #[DataProvider('provideReplacementDocuments')]
    #[DoesNotPerformAssertions]
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

    #[DataProvider('provideInvalidDocumentValues')]
    public function testReplaceOneReplacementArgumentTypeCheck($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[1\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], $replacement]],
        ]);
    }

    #[DataProvider('provideUpdateDocuments')]
    public function testReplaceOneReplacementArgumentProhibitsUpdateDocument($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $operations[0]["replaceOne"][1] is an update operator');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], $replacement]],
        ]);
    }

    #[DataProvider('provideUpdatePipelines')]
    #[DataProvider('provideEmptyUpdatePipelinesExcludingArray')]
    public function testReplaceOneReplacementArgumentProhibitsUpdatePipeline($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#(\$operations\[0\]\["replaceOne"\]\[1\] is an update pipeline)|(\$operations\[0\]\["replaceOne"\]\[1\] to have type "document" \(array or object\))#');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], $replacement]],
        ]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testReplaceOneCollationOptionTypeCheck($collation): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[2\]\["collation"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], ['y' => 1], ['collation' => $collation]]],
        ]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testReplaceOneSortOptionTypeCheck($sort): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[2\]\["sort"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], ['y' => 1], ['sort' => $sort]]],
        ]);
    }

    #[DataProvider('provideInvalidBooleanValues')]
    public function testReplaceOneUpsertOptionTypeCheck($upsert): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["replaceOne"\]\[2\]\["upsert"\] to have type "boolean" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::REPLACE_ONE => [['x' => 1], ['y' => 1], ['upsert' => $upsert]]],
        ]);
    }

    public static function provideInvalidBooleanValues()
    {
        return self::wrapValuesForDataProvider(self::getInvalidBooleanValues());
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

    #[DataProvider('provideInvalidDocumentValues')]
    public function testUpdateManyFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateMany"\]\[0\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [$filter, ['$set' => ['x' => 1]]]],
        ]);
    }

    #[DataProvider('provideUpdateDocuments')]
    #[DataProvider('provideUpdatePipelines')]
    #[DoesNotPerformAssertions]
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

    #[DataProvider('provideInvalidDocumentValues')]
    public function testUpdateManyUpdateArgumentTypeCheck($update): void
    {
        $this->expectException($update instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], $update]],
        ]);
    }

    #[DataProvider('provideReplacementDocuments')]
    #[DataProvider('provideEmptyUpdatePipelines')]
    public function testUpdateManyUpdateArgumentProhibitsReplacementDocumentOrEmptyPipeline($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected update operator(s) or non-empty pipeline for $operations[0]["updateMany"][1]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], $update]],
        ]);
    }

    #[DataProvider('provideInvalidArrayValues')]
    public function testUpdateManyArrayFiltersOptionTypeCheck($arrayFilters): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["arrayFilters"\] to have type "array" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['arrayFilters' => $arrayFilters]]],
        ]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testUpdateManyCollationOptionTypeCheck($collation): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["collation"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['collation' => $collation]]],
        ]);
    }

    public function testUpdateManyProhibitsSortOption(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"sort" option cannot be used with $operations[0]["updateMany"]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['y' => 1]], ['sort' => ['z' => 1]]]],
        ]);
    }

    #[DataProvider('provideInvalidBooleanValues')]
    public function testUpdateManyUpsertOptionTypeCheck($upsert): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateMany"\]\[2\]\["upsert"\] to have type "boolean" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_MANY => [['x' => 1], ['$set' => ['x' => 1]], ['upsert' => $upsert]]],
        ]);
    }

    #[DataProvider('provideUpdateDocuments')]
    #[DataProvider('provideUpdatePipelines')]
    #[DoesNotPerformAssertions]
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

    #[DataProvider('provideInvalidDocumentValues')]
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

    #[DataProvider('provideInvalidDocumentValues')]
    public function testUpdateOneUpdateArgumentTypeCheck($update): void
    {
        $this->expectException($update instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], $update]],
        ]);
    }

    #[DataProvider('provideReplacementDocuments')]
    #[DataProvider('provideEmptyUpdatePipelines')]
    public function testUpdateOneUpdateArgumentProhibitsReplacementDocumentOrEmptyPipeline($update): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected update operator(s) or non-empty pipeline for $operations[0]["updateOne"][1]');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], $update]],
        ]);
    }

    #[DataProvider('provideInvalidArrayValues')]
    public function testUpdateOneArrayFiltersOptionTypeCheck($arrayFilters): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["arrayFilters"\] to have type "array" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['arrayFilters' => $arrayFilters]]],
        ]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testUpdateOneCollationOptionTypeCheck($collation): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["collation"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['collation' => $collation]]],
        ]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testUpdateOneSortOptionTypeCheck($sort): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["sort"\] to have type "document" \(array or object\) but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['y' => 1]], ['sort' => $sort]]],
        ]);
    }

    #[DataProvider('provideInvalidBooleanValues')]
    public function testUpdateOneUpsertOptionTypeCheck($upsert): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$operations\[0\]\["updateOne"\]\[2\]\["upsert"\] to have type "boolean" but found ".+"/');
        new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), [
            [BulkWrite::UPDATE_ONE => [['x' => 1], ['$set' => ['x' => 1]], ['upsert' => $upsert]]],
        ]);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
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

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'bypassDocumentValidation' => self::getInvalidBooleanValues(),
            'codec' => self::getInvalidDocumentCodecValues(),
            'ordered' => self::getInvalidBooleanValues(true),
            'session' => self::getInvalidSessionValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }
}
