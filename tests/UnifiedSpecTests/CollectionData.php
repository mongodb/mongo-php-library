<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use ArrayIterator;
use IteratorIterator;
use MongoDB\Client;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\UnifiedSpecTests\Constraint\Matches;
use MultipleIterator;
use stdClass;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertThat;
use function sprintf;

class CollectionData
{
    /** @var string */
    private $collectionName;

    /** @var string */
    private $databaseName;

    /** @var array */
    private $documents;

    public function __construct(stdClass $o)
    {
        assertIsString($o->collectionName);
        $this->collectionName = $o->collectionName;

        assertIsString($o->databaseName);
        $this->databaseName = $o->databaseName;

        assertIsArray($o->documents);
        assertContainsOnly('object', $o->documents);
        $this->documents = $o->documents;
    }

    public function prepareInitialData(Client $client)
    {
        $database = $client->selectDatabase(
            $this->databaseName,
            ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]
        );

        $database->dropCollection($this->collectionName);

        if (empty($this->documents)) {
            $database->createCollection($this->collectionName);

            return;
        }

        $database->selectCollection($this->collectionName)->insertMany($this->documents);
    }

    public function assertOutcome(Client $client)
    {
        $collection = $client->selectCollection(
            $this->databaseName,
            $this->collectionName,
            [
                'readConcern' => new ReadConcern(ReadConcern::LOCAL),
                'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
            ]
        );

        $cursor = $collection->find([], ['sort' => ['_id' => 1]]);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($this->documents));
        $mi->attachIterator(new IteratorIterator($cursor));

        foreach ($mi as $i => $documents) {
            list($expectedDocument, $actualDocument) = $documents;
            assertNotNull($expectedDocument);
            assertNotNull($actualDocument);

            /* Prohibit extra root keys and disable operators to enforce exact
             * matching of documents. Key order variation is still allowed. */
            $constraint = new Matches($expectedDocument, null, false, false);
            assertThat($actualDocument, $constraint, sprintf('documents[%d] match', $i));
        }
    }
}
