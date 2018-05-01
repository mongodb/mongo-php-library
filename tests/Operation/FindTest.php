<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Find;

class FindTest extends TestCase
{
    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        $this->expectException(InvalidArgumentException::class);
        new Find($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
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

        foreach ($this->getInvalidStringValues() as $value) {
            $options[][] = ['comment' => $value];
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

    public function testSnapshotOptionIsDeprecated()
    {
        $this->assertDeprecated(function() {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['snapshot' => true]);
        });

        $this->assertDeprecated(function() {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['snapshot' => false]);
        });
    }

    public function testMaxScanOptionIsDeprecated()
    {
        $this->assertDeprecated(function() {
            new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['maxScan' => 1]);
        });
    }

    private function getInvalidHintValues()
    {
        return [123, 3.14, true];
    }

    /**
     * @dataProvider provideInvalidConstructorCursorTypeOptions
     */
    public function testConstructorCursorTypeOption($cursorType)
    {
        $this->expectException(InvalidArgumentException::class);
        new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['cursorType' => $cursorType]);
    }

    public function provideInvalidConstructorCursorTypeOptions()
    {
        return $this->wrapValuesForDataProvider([-1, 0, 4]);
    }
}
