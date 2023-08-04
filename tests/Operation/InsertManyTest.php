<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Operation\InsertMany;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;

class InsertManyTest extends TestCase
{
    public function testConstructorDocumentsMustNotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$documents is empty');
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), []);
    }

    public function testConstructorDocumentsMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$documents is not a list');
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [1 => ['x' => 1]]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorDocumentsArgumentElementTypeChecks($document): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected \$documents[0\] to have type "array or object" but found "[\w ]+"/');
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [$document]);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [['x' => 1]], $options);
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

    public function testCodecRejectsInvalidDocuments(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue([]));

        new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [['x' => 1]], ['codec' => new TestDocumentCodec()]);
    }
}
