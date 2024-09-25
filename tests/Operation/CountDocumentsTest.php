<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CountDocuments;
use TypeError;

class CountDocumentsTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException($filter instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new CountDocuments($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CountDocuments($this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'collation' => self::getInvalidDocumentValues(),
            'hint' => self::getInvalidHintValues(),
            'limit' => self::getInvalidIntegerValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'readConcern' => self::getInvalidReadConcernValues(),
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'session' => self::getInvalidSessionValues(),
            'skip' => self::getInvalidIntegerValues(),
        ]);
    }
}
