<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Javascript;
use MongoDB\BSON\ObjectId;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\MapReduce;
use stdClass;

class MapReduceTest extends TestCase
{
    /**
     * @dataProvider provideInvalidOutValues
     */
    public function testConstructorOutArgumentTypeCheck($out)
    {
        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');

        $this->expectException(InvalidArgumentException::class);
        new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out);
    }

    public function provideInvalidOutValues()
    {
        return $this->wrapValuesForDataProvider([123, 3.14, true]);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $this->expectException(InvalidArgumentException::class);
        new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out, $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['bypassDocumentValidation' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['collation' => $value];
        }

        foreach ($this->getInvalidJavascriptValues() as $value) {
            $options[][] = ['finalize' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['jsMode' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['limit' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['maxTimeMS' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['query' => $value];
        }

        foreach ($this->getInvalidReadConcernValues() as $value) {
            $options[][] = ['readConcern' => $value];
        }

        foreach ($this->getInvalidReadPreferenceValues() as $value) {
            $options[][] = ['readPreference' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['scope' => $value];
        }

        foreach ($this->getInvalidSessionValues() as $value) {
            $options[][] = ['session' => $value];
        }

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['sort' => $value];
        }

        foreach ($this->getInvalidArrayValues() as $value) {
            $options[][] = ['typeMap' => $value];
        }

        foreach ($this->getInvalidBooleanValues() as $value) {
            $options[][] = ['verbose' => $value];
        }

        foreach ($this->getInvalidWriteConcernValues() as $value) {
            $options[][] = ['writeConcern' => $value];
        }

        return $options;
    }

    private function getInvalidJavascriptValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass(), new ObjectId()];
    }
}
