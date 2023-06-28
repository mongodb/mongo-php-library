<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\Javascript;
use MongoDB\BSON\ObjectId;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\MapReduce;
use stdClass;

class MapReduceTest extends TestCase
{
    /** @dataProvider provideInvalidOutValues */
    public function testConstructorOutArgumentTypeCheck($out): void
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

    /** @dataProvider provideDeprecatedOutValues */
    public function testConstructorOutArgumentDeprecations($out): void
    {
        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');

        $this->assertDeprecated(function () use ($map, $reduce, $out): void {
            new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out);
        });
    }

    public function provideDeprecatedOutValues(): array
    {
        return [
            'nonAtomic:array' => [['nonAtomic' => false]],
            'nonAtomic:object' => [(object) ['nonAtomic' => false]],
            'nonAtomic:Serializable' => [new BSONDocument(['nonAtomic' => false])],
            'nonAtomic:Document' => [Document::fromPHP(['nonAtomic' => false])],
            'sharded:array' => [['sharded' => false]],
            'sharded:object' => [(object) ['sharded' => false]],
            'sharded:Serializable' => [new BSONDocument(['sharded' => false])],
            'sharded:Document' => [Document::fromPHP(['sharded' => false])],
        ];
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $this->expectException(InvalidArgumentException::class);
        new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out, $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'bypassDocumentValidation' => $this->getInvalidBooleanValues(),
            'collation' => $this->getInvalidDocumentValues(),
            'finalize' => $this->getInvalidJavascriptValues(),
            'jsMode' => $this->getInvalidBooleanValues(),
            'limit' => $this->getInvalidIntegerValues(),
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'query' => $this->getInvalidDocumentValues(),
            'readConcern' => $this->getInvalidReadConcernValues(),
            'readPreference' => $this->getInvalidReadPreferenceValues(),
            'scope' => $this->getInvalidDocumentValues(),
            'session' => $this->getInvalidSessionValues(),
            'sort' => $this->getInvalidDocumentValues(),
            'typeMap' => $this->getInvalidArrayValues(),
            'verbose' => $this->getInvalidBooleanValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
        ]);
    }

    private function getInvalidJavascriptValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass(), new ObjectId()];
    }
}
