<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Aggregate;
use PHPUnit\Framework\Attributes\DataProvider;

class AggregateTest extends TestCase
{
    public function testConstructorPipelineArgumentMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$pipeline is not a valid aggregation pipeline');
        new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [1 => ['$match' => ['x' => 1]]]);
    }

    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [['$match' => ['x' => 1]]], $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'allowDiskUse' => self::getInvalidBooleanValues(),
            'batchSize' => self::getInvalidIntegerValues(),
            'bypassDocumentValidation' => self::getInvalidBooleanValues(),
            'codec' => self::getInvalidDocumentCodecValues(),
            'collation' => self::getInvalidDocumentValues(),
            'hint' => self::getInvalidHintValues(),
            'let' => self::getInvalidDocumentValues(),
            'explain' => self::getInvalidBooleanValues(),
            'maxAwaitTimeMS' => self::getInvalidIntegerValues(),
            'maxTimeMS' => self::getInvalidIntegerValues(),
            'readConcern' => self::getInvalidReadConcernValues(),
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'session' => self::getInvalidSessionValues(),
            'typeMap' => self::getInvalidArrayValues(),
            'writeConcern' => self::getInvalidWriteConcernValues(),
        ]);
    }

    public function testExplainableCommandDocument(): void
    {
        $options = [
            'allowDiskUse' => true,
            'batchSize' => 100,
            'bypassDocumentValidation' => true,
            'collation' => ['locale' => 'fr'],
            'comment' => 'explain me',
            'hint' => '_id_',
            'let' => ['a' => 1],
            'maxTimeMS' => 100,
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            // Intentionally omitted options
            // The "explain" option is illegal
            'readPreference' => new ReadPreference(ReadPreference::SECONDARY_PREFERRED),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
            'writeConcern' => new WriteConcern(0),
        ];
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [['$project' => ['_id' => 0]]], $options);

        $expected = [
            'aggregate' => $this->getCollectionName(),
            'pipeline' => [['$project' => ['_id' => 0]]],
            'allowDiskUse' => true,
            'bypassDocumentValidation' => true,
            'collation' => (object) ['locale' => 'fr'],
            'comment' => 'explain me',
            'hint' => '_id_',
            'maxTimeMS' => 100,
            'readConcern' => new ReadConcern(ReadConcern::LOCAL),
            'let' => (object) ['a' => 1],
            'cursor' => ['batchSize' => 100],
        ];
        $this->assertEquals($expected, $operation->getCommandDocument());
    }
}
