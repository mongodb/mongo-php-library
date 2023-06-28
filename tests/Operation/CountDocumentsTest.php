<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CountDocuments;

class CountDocumentsTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CountDocuments($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CountDocuments($this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'collation' => $this->getInvalidDocumentValues(),
            'hint' => $this->getInvalidHintValues(),
            'limit' => $this->getInvalidIntegerValues(),
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'readConcern' => $this->getInvalidReadConcernValues(),
            'readPreference' => $this->getInvalidReadPreferenceValues(),
            'session' => $this->getInvalidSessionValues(),
            'skip' => $this->getInvalidIntegerValues(),
        ]);
    }
}
