<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Distinct;

class DistinctTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', $filter);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', [], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'collation' => $this->getInvalidDocumentValues(),
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'readConcern' => $this->getInvalidReadConcernValues(),
            'readPreference' => $this->getInvalidReadPreferenceValues(),
            'session' => $this->getInvalidSessionValues(),
            'typeMap' => $this->getInvalidArrayValues(),
        ]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'collation' => ['locale' => 'fr'],
            'maxTimeMS' => 100,
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'comment' => 'explain me',
            // Intentionally omitted options
            'readPreference' => new ReadPreference(ReadPreference::SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
        ];
        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'f', ['x' => 1], $options);

        $expected = [
            'distinct' => $this->getCollectionName(),
            'key' => 'f',
            'query' => (object) ['x' => 1],
            'collation' => (object) ['locale' => 'fr'],
            'comment' => 'explain me',
            'maxTimeMS' => 100,
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
