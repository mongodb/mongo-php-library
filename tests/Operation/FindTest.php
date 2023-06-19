<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Find;

class FindTest extends TestCase
{
    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Find($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Find($this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['allowPartialResults' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['batchSize' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['collation' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['cursorType' => $value];
        }

        foreach ($this->getInvalidHintValues() as $value) {
            $options[][] = ['hint' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['limit' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['max' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxAwaitTimeMS' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxScan' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxTimeMS' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['min' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['modifiers' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['oplogReplay' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['projection' => $value];
        }

        foreach ($this->getInvalidReadConcernValues() as $value) {
            $options[][] = ['readConcern' => $value];
        }

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['returnKey' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['showRecordId' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['skip' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['snapshot' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['sort' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        return $options;
    }

    public function testSnapshotOptionIsDeprecated(): void
    {
        $this->assertDeprecated(function (): void {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['snapshot' => true]);
        });

        $this->assertDeprecated(function (): void {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['snapshot' => false]);
        });
    }

    public function testMaxScanOptionIsDeprecated(): void
    {
        $this->assertDeprecated(function (): void {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['maxScan' => 1]);
        });
    }

    private function getInvalidHintValues()
    {
        return [123, 3.14, true];
    }

    /** @dataProvider provideInvalidConstructorCursorTypeOptions */
    public function testConstructorCursorTypeOption($cursorType): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['cursorType' => $cursorType]);
    }

    public function provideInvalidConstructorCursorTypeOptions()
    {
        return $this->wrapValuesForDataProvider([-1, 0, 4]);
    }

    public function testExplainableCommandDocument(): void
    {
        // all options except deprecated "snapshot" and "maxScan"
        $options = [
            'allowDiskUse' => true,
            'allowPartialResults' => true,
            'batchSize' => 123,
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'hint' => '_id_',
            'limit' => 15,
            'max' => ['x' => 100],
            'maxTimeMS' => 100,
            'min' => ['x' => 10],
            'noCursorTimeout' => true,
            'oplogReplay' => true,
            'projection' => ['_id' => 0],
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'returnKey' => true,
            'showRecordId' => true,
            'skip' => 5,
            'sort' => ['x' => 1],
            'let' => ['y' => 2],
            // Intentionally omitted options
            'cursorType' => Find::NON_TAILABLE,
            'maxAwaitTimeMS' => 500,
            'modifiers' => ['foo' => 'bar'],
            'readPreference' => new ReadPreference(ReadPreference::SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array'],
        ];
        $operation = new Find($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1], $options);

        $expected = [
            'find' => $this->getCollectionName(),
            'filter' => (object) ['x' => 1],
            'allowDiskUse' => true,
            'allowPartialResults' => true,
            'batchSize' => 123,
            'comment' => 'explain me',
            'hint' => '_id_',
            'limit' => 15,
            'maxTimeMS' => 100,
            'noCursorTimeout' => true,
            'oplogReplay' => true,
            'projection' => ['_id' => 0],
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'returnKey' => true,
            'showRecordId' => true,
            'skip' => 5,
            'sort' => ['x' => 1],
            'collation' => (object) ['locale' => 'fr'],
            'let' => (object) ['y' => 2],
            'max' => (object) ['x' => 100],
            'min' => (object) ['x' => 10],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
