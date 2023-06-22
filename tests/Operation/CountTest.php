<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Count;

class CountTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Count($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Count($this->getDatabaseName(), $this->getCollectionName(), [], $options);
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

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'hint' => '_id_',
            'limit' => 10,
            'skip' => 20,
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'maxTimeMS' => 100,
        ];
        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $options);

        $expected = [
            'count' => $this->getCollectionName(),
            'query' => (object) ['x' => 1],
            'collation' => (object) ['locale' => 'fr'],
            'hint' => '_id_',
            'comment' => 'explain me',
            'limit' => 10,
            'skip' => 20,
            'maxTimeMS' => 100,
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
