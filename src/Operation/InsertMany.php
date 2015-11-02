<?php

namespace MongoDB\Operation;

use MongoDB\InsertManyResult;
use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\InvalidArgumentTypeException;

/**
 * Operation for inserting multiple documents with the insert command.
 *
 * @api
 * @see MongoDB\Collection::insertMany()
 * @see http://docs.mongodb.org/manual/reference/command/insert/
 */
class InsertMany implements Executable
{
    private $databaseName;
    private $collectionName;
    private $documents;
    private $options;

    /**
     * Constructs an insert command.
     *
     * Supported options:
     *
     *  * ordered (boolean): If true, when an insert fails, return without
     *    performing the remaining writes. If false, when a write fails,
     *    continue with the remaining writes, if any. The default is true.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param string           $databaseName   Database name
     * @param string           $collectionName Collection name
     * @param array[]|object[] $documents      List of documents to insert
     * @param array            $options        Command options
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, array $documents, array $options = array())
    {
        if (empty($documents)) {
            throw new InvalidArgumentException('$documents is empty');
        }

        $expectedIndex = 0;

        foreach ($documents as $i => $document) {
            if ($i !== $expectedIndex) {
                throw new InvalidArgumentException(sprintf('$documents is not a list (unexpected index: "%s")', $i));
            }

            if ( ! is_array($document) && ! is_object($document)) {
                throw new InvalidArgumentTypeException(sprintf('$documents[%d]', $i), $document, 'array or object');
            }

            $expectedIndex += 1;
        }

        $options += array(
            'ordered' => true,
        );

        if ( ! is_bool($options['ordered'])) {
            throw new InvalidArgumentTypeException('"ordered" option', $options['ordered'], 'boolean');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw new InvalidArgumentTypeException('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->documents = $documents;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return InsertManyResult
     */
    public function execute(Server $server)
    {
        $bulk = new Bulk(['ordered' => $this->options['ordered']]);
        $insertedIds = array();

        foreach ($this->documents as $i => $document) {
            $insertedId = $bulk->insert($document);

            if ($insertedId !== null) {
                $insertedIds[$i] = $insertedId;
            } else {
                // TODO: This may be removed if PHPC-382 is implemented
                $insertedIds[$i] = is_array($document) ? $document['_id'] : $document->_id;
            }
        }

        $writeConcern = isset($this->options['writeConcern']) ? $this->options['writeConcern'] : null;
        $writeResult = $server->executeBulkWrite($this->databaseName . '.' . $this->collectionName, $bulk, $writeConcern);

        return new InsertManyResult($writeResult, $insertedIds);
    }
}
