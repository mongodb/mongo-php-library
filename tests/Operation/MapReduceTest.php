<?php

declare(strict_types=1);

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\Javascript;
use MongoDB\BSON\ObjectId;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\MapReduce;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use TypeError;

class MapReduceTest extends TestCase
{
    #[DataProvider('provideInvalidOutValues')]
    public function testConstructorOutArgumentTypeCheck($out): void
    {
        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');

        $this->expectException(TypeError::class);
        new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out);
    }

    public static function provideInvalidOutValues()
    {
        return self::wrapValuesForDataProvider([123, 3.14, true]);
    }

    #[DataProvider('provideDeprecatedOutValues')]
    public function testConstructorOutArgumentDeprecations($out): void
    {
        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');

        $this->assertDeprecated(function () use ($map, $reduce, $out): void {
            new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out);
        });
    }

    public static function provideDeprecatedOutValues(): array
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

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $this->expectException(InvalidArgumentException::class);
        new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out, $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'bypassDocumentValidation' => self::getInvalidBooleanValues(),
            'collation' => self::getInvalidDocumentValues(),
            'finalize' => self::getInvalidJavascriptValues(),
            'jsMode' => self::getInvalidBooleanValues(),
            'limit' => self::getInvalidIntegerValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'query' => self::getInvalidDocumentValues(),
            'readConcern' => self::getInvalidReadConcernValues(),
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'scope' => self::getInvalidDocumentValues(),
            'session' => self::getInvalidSessionValues(),
            'sort' => self::getInvalidDocumentValues(),
            'typeMap' => self::getInvalidArrayValues(),
            'verbose' => self::getInvalidBooleanValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    private static function getInvalidJavascriptValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass(), new ObjectId()];
    }
}
