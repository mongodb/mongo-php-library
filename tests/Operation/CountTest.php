<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\PackedArray;
use MongoDB\Driver\ReadConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Count;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeError;

class CountTest extends TestCase
{
    #[DataProvider('provideInvalidDocumentValues')]
    public function testConstructorFilterArgumentTypeCheck($filter): void
    {
        $this->expectException($filter instanceof PackedArray ? InvalidArgumentException::class : TypeError::class);
        new Count($this->getDatabaseName(), $this->getCollectionName(), $filter);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Count($this->getDatabaseName(), $this->getCollectionName(), [], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'collation' => self::getInvalidDocumentValues(),
            'hint' => self::getInvalidHintValues(),
            'limit' => self::getInvalidIntegerValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'readConcern' => self::getInvalidReadConcernValues(),
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'session' => self::getInvalidSessionValues(),
            'skip' => self::getInvalidIntegerValues(),
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
