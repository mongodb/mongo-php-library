<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use ArrayIterator;
use MongoDB\Client;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\UnifiedSpecTests\Constraint\Matches;
use MultipleIterator;
use stdClass;

use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertObjectNotHasProperty;
use function PHPUnit\Framework\assertThat;
use function sprintf;

class CollectionData
{
    private string $collectionName;

    private string $databaseName;

    private array $documents;

    private array $createOptions = [];

    public function __construct(stdClass $o)
    {
        assertIsString($o->collectionName);
        $this->collectionName = $o->collectionName;

        assertIsString($o->databaseName);
        $this->databaseName = $o->databaseName;

        assertIsArray($o->documents);
        assertContainsOnly('object', $o->documents);
        $this->documents = $o->documents;

        if (isset($o->createOptions)) {
            assertIsObject($o->createOptions);
            /* The writeConcern option is prohibited here, as prepareInitialData() applies w:majority. Since a session
             * option would be ignored by prepareInitialData() we can assert that it is also omitted. */
            assertObjectNotHasProperty('writeConcern', $o->createOptions);
            assertObjectNotHasProperty('session', $o->createOptions);
            $this->createOptions = (array) $o->createOptions;
        }
    }

    public function prepareInitialData(Client $client, ?Session $session = null): void
    {
        $database = $client->selectDatabase(
            $this->databaseName,
            ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)],
        );

        $database->dropCollection($this->collectionName, ['session' => $session]);

        if (empty($this->documents) || ! empty($this->createOptions)) {
            $database->createCollection($this->collectionName, ['session' => $session] + $this->createOptions);
        }

        if (! empty($this->documents)) {
            $database->selectCollection($this->collectionName)->insertMany($this->documents, ['session' => $session]);
        }
    }

    public function assertOutcome(Client $client): void
    {
        $collection = $client->selectCollection(
            $this->databaseName,
            $this->collectionName,
            [
                'readConcern' => new ReadConcern(ReadConcern::LOCAL),
                'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
            ],
        );

        $cursor = $collection->find([], ['sort' => ['_id' => 1]]);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($this->documents));
        $mi->attachIterator($cursor);

        foreach ($mi as $i => $documents) {
            [$expectedDocument, $actualDocument] = $documents;
            assertNotNull($expectedDocument);
            assertNotNull($actualDocument);

            /* Prohibit extra root keys and disable operators to enforce exact
             * matching of documents. Key order variation is still allowed. */
            $constraint = new Matches($expectedDocument, null, false, false);
            assertThat($actualDocument, $constraint, sprintf('documents[%d] match', $i));
        }
    }
}
