<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Aggregate;

class AggregateTest extends TestCase
{
    public function testConstructorPipelineArgumentMustBeAList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$pipeline is not a valid aggregation pipeline');
        new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [1 => ['$match' => ['x' => 1]]]);
    }

    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [['$match' => ['x' => 1]]], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'allowDiskUse' => $this->getInvalidBooleanValues(),
            'batchSize' => $this->getInvalidIntegerValues(),
            'bypassDocumentValidation' => $this->getInvalidBooleanValues(),
            'codec' => $this->getInvalidDocumentCodecValues(),
            'collation' => $this->getInvalidDocumentValues(),
            'hint' => $this->getInvalidHintValues(),
            'let' => $this->getInvalidDocumentValues(),
            'explain' => $this->getInvalidBooleanValues(),
            'maxAwaitTimeMS' => $this->getInvalidIntegerValues(),
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'readConcern' => $this->getInvalidReadConcernValues(),
            'readPreference' => $this->getInvalidReadPreferenceValues(),
            'session' => $this->getInvalidSessionValues(),
            'typeMap' => $this->getInvalidArrayValues(),
            'writeConcern' => $this->getInvalidWriteConcernValues(),
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
