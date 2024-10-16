<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Operation\InsertOne;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

class InsertOneTest extends TestCase
{
    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorDocumentArgumentTypeCheck($document): void
    {
        $this->expectException($document instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'bypassDocumentValidation' => self::getInvalidBooleanValues(),
            'codec' => self::getInvalidDocumentCodecValues(),
            'session' => self::getInvalidSessionValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    public function testCodecRejectsInvalidDocuments(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue([]));

        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['codec' => new TestDocumentCodec()]);
    }
}
