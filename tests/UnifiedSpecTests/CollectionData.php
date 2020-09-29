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
        assertInternalType('string', $o->collectionName);
        $this->collectionName = $o->collectionName;

        assertInternalType('string', $o->databaseName);
        $this->databaseName = $o->databaseName;

        assertInternalType('array', $o->documents);
        assertContainsOnly('object', $o->documents);
        $this->documents = $o->documents;
    }

    /**
     * Prepare collection state for "initialData".
     *
     * @param Client $client
     */
    public function prepare(Client $client)
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

        $collection = $database->selectCollection($this->collectionName);
        $collection->insertMany($this->documents);
    }

    /**
     * Assert collection contents for "outcome".
     *
     * @param Client $client
     */
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

            /* Disable extra root keys and operators when matching, which is
             * effectively an exact match that allows key order variation. */
            $constraint = new Matches($expectedDocument, null, false, false);
            assertThat($actualDocument, $constraint, sprintf('documents[%d] match', $i));
        }
    }
}
