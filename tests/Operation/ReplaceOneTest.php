<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Operation\ReplaceOne;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use TypeError;

class ReplaceOneTest extends TestCase
{
    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException($filter instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), $filter, ['y' => 1]);
    }

    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorReplacementArgumentTypeCheck($replacement): void
    {
        $this->expectException($replacement instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    #[DataProvider('provideReplacementDocuments')]
    #[DoesNotPerformAssertions]
    public function testConstructorReplacementArgument($replacement): void
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    #[DataProvider('provideUpdateDocuments')]
    public function testConstructorReplacementArgumentProhibitsUpdateDocument($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $replacement is an update operator');
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    #[DataProvider('provideUpdatePipelines')]
    #[DataProvider('provideEmptyUpdatePipelinesExcludingArray')]
    public function testConstructorReplacementArgumentProhibitsUpdatePipeline($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#(\$replacement is an update pipeline)|(Expected \$replacement to have type "document" \(array or object\))#');
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionsTypeCheck($options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['y' => 1], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'codec' => self::getInvalidDocumentCodecValues(),
        ]);
    }

    public function testCodecRejectsInvalidDocuments(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue([]));

        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['y' => 1], ['codec' => new TestDocumentCodec()]);
    }
}
