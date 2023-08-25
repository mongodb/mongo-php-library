<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Operation\InsertOne;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;

class InsertOneTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorDocumentArgumentTypeCheck($document): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'bypassDocumentValidation' => $this->getInvalidBooleanValues(),
            'codec' => $this->getInvalidDocumentCodecValues(),
            'session' => $this->getInvalidSessionValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
        ]);
    }

    public function testCodecRejectsInvalidDocuments(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue([]));

        new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], ['codec' => new TestDocumentCodec()]);
    }
}
