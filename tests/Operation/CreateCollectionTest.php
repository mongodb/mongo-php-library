<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CreateCollection;
use PHPUnit\Framework\Attributes\DataProvider;

class CreateCollectionTest extends TestCase
{
    public function testConstructorPipelineOptionMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"pipeline" option is not a valid aggregation pipeline');
        new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), ['pipeline' => [1 => ['$match' => ['x' => 1]]]]);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'capped' => self::getInvalidBooleanValues(),
            'changeStreamPreAndPostImages' => self::getInvalidDocumentValues(),
            'clusteredIndex' => self::getInvalidDocumentValues(),
            'collation' => self::getInvalidDocumentValues(),
            'encryptedFields' => self::getInvalidDocumentValues(),
            'expireAfterSeconds' => self::getInvalidIntegerValues(),
            'indexOptionDefaults' => self::getInvalidDocumentValues(),
            'max' => self::getInvalidIntegerValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'pipeline' => self::getInvalidArrayValues(),
            'session' => self::getInvalidSessionValues(),
            'size' => self::getInvalidIntegerValues(),
            'storageEngine' => self::getInvalidDocumentValues(),
            'timeseries' => self::getInvalidDocumentValues(),
            'validationAction' => self::getInvalidStringValues(),
            'validationLevel' => self::getInvalidStringValues(),
            'validator' => self::getInvalidDocumentValues(),
            'viewOn' => self::getInvalidStringValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }
}
