<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CreateCollection;

class CreateCollectionTest extends TestCase
{
    public function testConstructorPipelineOptionMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"pipeline" option is not a valid aggregation pipeline');
        new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), ['pipeline' => [1 => ['$match' => ['x' => 1]]]]);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'autoIndexId' => $this->getInvalidBooleanValues(),
            'capped' => $this->getInvalidBooleanValues(),
            'changeStreamPreAndPostImages' => $this->getInvalidDocumentValues(),
            'clusteredIndex' => $this->getInvalidDocumentValues(),
            'collation' => $this->getInvalidDocumentValues(),
            'encryptedFields' => $this->getInvalidDocumentValues(),
            'expireAfterSeconds' => $this->getInvalidIntegerValues(),
            'flags' => $this->getInvalidIntegerValues(),
            'indexOptionDefaults' => $this->getInvalidDocumentValues(),
            'max' => $this->getInvalidIntegerValues(),
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'pipeline' => $this->getInvalidArrayValues(),
            'session' => $this->getInvalidSessionValues(),
            'size' => $this->getInvalidIntegerValues(),
            'storageEngine' => $this->getInvalidDocumentValues(),
            'timeseries' => $this->getInvalidDocumentValues(),
            'typeMap' => $this->getInvalidArrayValues(),
            'validationAction' => $this->getInvalidStringValues(),
            'validationLevel' => $this->getInvalidStringValues(),
            'validator' => $this->getInvalidDocumentValues(),
            'viewOn' => $this->getInvalidStringValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
        ]);
    }

    public function testAutoIndexIdOptionIsDeprecated(): void
    {
        $this->assertDeprecated(function (): void {
            new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), ['autoIndexId' => true]);
        });

        $this->assertDeprecated(function (): void {
            new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), ['autoIndexId' => false]);
        });
    }
}
