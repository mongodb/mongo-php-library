<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CreateCollection;

class CreateCollectionTest extends TestCase
{
    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['autoIndexId' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['capped' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['collation' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['flags' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['indexOptionDefaults' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['max' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxTimeMS' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['size' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['storageEngine' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        foreach ($this->getInvalidStringValues() as $value) {
            $options[][] = ['validationAction' => $value];
        }

        foreach ($this->getInvalidStringValues() as $value) {
            $options[][] = ['validationLevel' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['validator' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }

    public function testAutoIndexIdOptionIsDeprecated()
    {
        $this->assertDeprecated(function() {
            new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), ['autoIndexId' => true]);
        });

        $this->assertDeprecated(function() {
            new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), ['autoIndexId' => false]);
        });
    }
}
