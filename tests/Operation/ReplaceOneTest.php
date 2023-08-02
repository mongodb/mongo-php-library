<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Operation\ReplaceOne;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;

class ReplaceOneTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), $filter, ['y' => 1]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorReplacementArgumentTypeCheck($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @doesNotPerformAssertions
     */
    public function testConstructorReplacementArgument($replacement): void
    {
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    /** @dataProvider provideUpdateDocuments */
    public function testConstructorReplacementArgumentProhibitsUpdateDocument($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $replacement is an update operator');
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    /**
     * @dataProvider provideUpdatePipelines
     * @dataProvider provideEmptyUpdatePipelinesExcludingArray
     */
    public function testConstructorReplacementArgumentProhibitsUpdatePipeline($replacement): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#(\$replacement is an update pipeline)|(Expected \$replacement to have type "document" \(array or object\))#');
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $replacement);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionsTypeCheck($options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['y' => 1], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'codec' => $this->getInvalidDocumentCodecValues(),
        ]);
    }

    public function testCodecRejectsInvalidDocuments(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue([]));

        new ReplaceOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['y' => 1], ['codec' => new TestDocumentCodec()]);
    }
}
