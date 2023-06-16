<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
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
        $options = [];

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['collation' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxTimeMS' => $value];
        }

        foreach ($this->getInvalidReadConcernValues() as $value) {
            $options[][] = ['readConcern' => $value];
        }

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        return $options;
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
